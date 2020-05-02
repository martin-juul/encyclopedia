<?php

namespace Tests\Unit\Jobs;

use App\Jobs\WikipediaArticleImportJob;
use Tests\TestCase;

class WikipediaArticleImportJobTest extends TestCase
{
    public function testHandle()
    {
        $job = $this->getJob();

        $job->handle();
    }

    private function getJob()
    {
        return new WikipediaArticleImportJob(__DIR__ . '/stubs/wikipedia-articleimportjobtest-stub.xml');
    }
}
