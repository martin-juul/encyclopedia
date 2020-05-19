<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\Article;
use App\Profiling\XDebugHooks;
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

    /** @var \Ds\Vector */
    private $create;
    /** @var \Ds\Map */
    private $update;

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

        $this->create = new Vector;
        $this->update = new Map;

        $parser = new Parser($this->path);

        foreach ($parser->read('mediawiki/page') as $node) {
            if (!$node) {
                break;
            }

            $article = $parser->parseNode($node);
            $article = $this->serializeArticle($article);

            if (!Article::whereArticleId($article['article_id'])->exists()) {
                $this->create->push($article);
            } else {
                $this->update->put($article['article_id'], $article);
            }

            unset($article);

            if ($this->create->count() >= $this->batchSize) {
                $this->createMany();
                $this->gcFlush();
            }

            if ($this->update->count() >= $this->batchSize) {
                $this->updateMany();
                $this->gcFlush();
            }

            if ($this->dbErrorCount >= self::MAX_ERRORS) {
                $this->fail();
            }
        }

        $this->createMany();
        $this->updateMany();

        if ($this->dbErrorCount >= self::MAX_ERRORS) {
            $this->fail();
        }

        unset(
            $this->path,
            $this->batchSize,
            $this->create,
            $this->update,
            $this->timeout,
            $this->dbErrorCount,
            $parser
        );

        Article::enableSearchSyncing();
        $this->gcFlush();
    }

    private function createMany(): void
    {
        if ($this->create->count() <= 0) {
            return;
        }

        $start = microtime(true);
        try {
            \DB::transaction(function () {
                Article::insert($this->create->toArray());
            });
        } catch (\Throwable $e) {
            $this->error('[DB]: transaction error during batch insert', [
                'exception' => [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'line'    => $e->getMessage(),
                ],
            ]);

            ++$this->dbErrorCount;
        }
        $stop = microtime(true);

        $time = bcsub($stop, $start);

        $this->info('[DB]: Insert transaction took ' . $time . ' sec');

        $this->create->clear();
        usleep(200000);
    }

    private function updateMany(): void
    {
        if ($this->update->count() <= 0) {
            return;
        }

        try {
            \DB::transaction(function () {
                foreach ($this->update as $key => $values) {
                    Article::whereArticleId($key)->update($values);
                }
            }, 3);
        } catch (\Throwable $e) {
            $this->error('[DB]: transaction error during batch update', [
                'exception' => [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'line'    => $e->getMessage(),
                ],
            ]);

            ++$this->dbErrorCount;
        }

        $this->update->clear();
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
