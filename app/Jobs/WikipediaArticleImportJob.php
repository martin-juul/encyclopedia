<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\Article;
use App\Profiling\XDebugHooks;
use App\Utilities\FlushableMap;
use App\Utilities\FlushableVector;
use App\Utilities\GC;
use App\WikiText\Models\WikiPage;
use App\WikiText\Parser\Parser;
use Carbon\Carbon;
use Ds\{Map, Vector};

class WikipediaArticleImportJob extends AbstractJob
{
    public $timeout = 0;

    /** @var string */
    protected $path;
    /** @var int */
    protected $batchSize;

    private $dbErrorCount = 0;
    private const MAX_ERRORS = 3;

    /**
     * Create a new job instance.
     *
     * @param string $path
     * @param int $batchSize
     */
    public function __construct(string $path, int $batchSize = 2000)
    {
        $this->path = $path;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Article::disableSearchSyncing();

        $createMany = fn(Vector $items) => $this->createMany($items);
        $updateMany = fn(Map $items) => $this->updateMany($items);

        $createBuffer = new FlushableVector($this->batchSize, $createMany);
        $updateBuffer = new FlushableMap($this->batchSize, $updateMany);

        $parser = new Parser($this->path);

        foreach ($parser->read('mediawiki/page') as $node) {
            if (!$node) {
                break;
            }

            $article = $parser->parseNode($node);
            $article = $this->serializeArticle($article);

            if (!Article::whereArticleId($article['article_id'])->exists()) {
                $createBuffer->push($article);
            } else {
                $updateBuffer->set($article['article_id'], $article);
            }

            unset($article);

            if ($this->dbErrorCount >= self::MAX_ERRORS) {
                $this->fail();
            }
        }

        if ($this->dbErrorCount >= self::MAX_ERRORS) {
            $this->fail();
        }

        $createBuffer->flush();
        $updateBuffer->flush();

        unset(
            $this->path,
            $this->batchSize,
            $this->timeout,
            $this->dbErrorCount,
            $createBuffer,
            $parser
        );

        Article::enableSearchSyncing();
        $this->gcFlush();
    }

    private function createMany(Vector $items): void
    {
        if (!$items instanceof Vector) {
            throw new \InvalidArgumentException('items must be a Ds\\Vector');
        }
        if ($items->count() <= 0) {
            return;
        }

        $start = microtime(true);

        try {
            \DB::transaction(function () use (&$items) {
                Article::insert($items->toArray());
            });
        } catch (\Throwable $e) {
            $this->errorE('[DB]: transaction error during batch insert', $e);

            ++$this->dbErrorCount;
        }

        $stop = microtime(true);
        $time = bcsub($stop, $start);

        $this->gcFlush();
        $this->info('[DB]: Insert transaction took ' . $time . ' sec');

        usleep(200000); // allow the database to recover for at bit
    }

    private function updateMany(Map $items): void
    {
        if (!$items instanceof Map) {
            throw new \InvalidArgumentException('items must be a Ds\\Map');
        }
        if ($items->count() <= 0) {
            return;
        }

        try {
            \DB::transaction(function () use (&$items) {
                foreach ($items as $key => $values) {
                    Article::whereArticleId($key)->update($values);
                }
            }, 3);
        } catch (\Throwable $e) {
            $this->errorE('[DB]: transaction error during batch update', $e);

            ++$this->dbErrorCount;
        }

        $this->gcFlush();
    }

    /**
     * @param \App\WikiText\Models\WikiPage $article
     *
     * @return array
     */
    private function serializeArticle(WikiPage $article): array
    {
        $data = [
            'title'             => $article->title,
            'article_id'        => $article->wikiText->id,
            'article_parent_id' => $article->wikiText->parent_id,
            'comment'           => $article->wikiText->comment,
            'text'              => $article->wikiText->text,
            'description'       => $article->shortDescription,
            'sha1'              => $article->wikiText->sha1,
            'redirect'          => $article->redirectTitle,
            'revision_time'     => Carbon::createFromTimeString($article->wikiText->timestamp),
        ];

        if (isset($article->wikiText->contributor)) {
            $data += [
                'contributor_id'       => $article->wikiText->contributor->id,
                'contributor_username' => $article->wikiText->contributor->username,
            ];
        } else {
            // Avoid SQL Error 42601 (VALUES lists must all be the same length)
            $data += [
                'contributor_id'       => null,
                'contributor_username' => null,
            ];
        }

        return $data;
    }

    private function gcFlush(): void
    {
        $this->xdebugStats();

        $bytes = GC::flushMemoryCaches();

        $this->info("[GC]: Flushed {$bytes} bytes");
    }

    private function xdebugStats()
    {
        if (!extension_loaded('Xdebug')) {
            return;
        }

        $mu = xdebug_memory_usage();
        $pmu = xdebug_peak_memory_usage();
        $gcRunCount = xdebug_get_gc_run_count();

        \Log::channel('stdout')->info("[XDebug]: (Mem) usage {$mu} peak {$pmu} | (GC) runs {$gcRunCount}");
    }
}
