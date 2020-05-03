<?php

namespace App\Jobs;

use App\Jobs\Traits\Logging;
use App\Models\Article;
use App\Utilities\Extensions\StrExt;
use App\Utilities\GC;
use App\WikiText\Models\WikiPage;
use App\WikiText\Parser\Parser;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WikipediaArticleImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Logging;

    public $timeout = 0;

    /** @var string */
    protected $path;

    private $create = [];
    private $update = [];

    private $dbErrorCount = 0;
    private const MAX_ERRORS = 3;

    /**
     * Create a new job instance.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Article::disableSearchSyncing();

        $parser = new Parser($this->path);

        foreach ($parser->read('mediawiki/page') as $node) {
            if (!$node) {
                break;
            }

            $article = $parser->parseNode($node);
            $article = $this->formatArticle($article);

            if (!Article::whereArticleId($article['article_id'])->exists()) {
                $this->create[] = $article;
            } else {
                $this->update[$article['article_id']] = $article;
            }

            unset($article);

            if (count($this->create) >= 2000) {
                $this->createMany();
                GC::flush();
            }

            if (count($this->update) >= 500) {
                $this->createMany();
                $this->updateMany();
                GC::flush();
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

        unset($this->create, $this->update, $this->timeout, $parser);

        Article::enableSearchSyncing();
        GC::flush();
    }

    private function createMany(): void
    {
        if (count($this->create) <= 0) {
            return;
        }

        try {
            \DB::transaction(function () {
                Article::insert($this->create);
            });
        } catch (\Throwable $e) {
            $this->error('Transaction error', [
                'exception' => [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'line'    => $e->getMessage(),
                ],
            ]);

            ++$this->dbErrorCount;
        }

        unset($this->create);

        $this->create = [];
    }

    private function updateMany(): void
    {
        if (count($this->update) <= 0) {
            return;
        }

        try {
            \DB::transaction(function () {
                foreach ($this->update as $key => $values) {
                    Article::whereArticleId($key)->update($values);
                }
            }, 3);
        } catch (\Throwable $e) {
            $this->error('Transaction error', [
                'exception' => [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'line'    => $e->getMessage(),
                ],
            ]);

            ++$this->dbErrorCount;
        }

        unset($this->update);

        $this->update = [];
    }

    /**
     * @param \App\WikiText\Models\WikiPage $article
     *
     * @return array
     */
    private function formatArticle(WikiPage $article): array
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
}
