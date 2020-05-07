<?php

namespace App\Listeners\Command;

use App\Models\Sys\PostgresDatabase;
use App\Models\Sys\ProfileReport;
use App\Profiling\Context\CommandLineContext;
use App\Profiling\XHProf;
use Illuminate\Console\Events\CommandFinished;

class FinishedListener
{
    private XHProf $xhprof;

    public function __construct(XHProf $XHProf)
    {
        $this->xhprof = $XHProf;
    }

    /**
     * Handle the event.
     *
     * @param \Illuminate\Console\Events\CommandFinished $event
     *
     * @return void
     */
    public function handle(CommandFinished $event): void
    {
        if (!config('profiling.enabled') || !PostgresDatabase::isMigrated()) {
            return;
        }

        $report = $this->xhprof->stop();
        if ($report === null) {
            return;
        }
        $context = new CommandLineContext($event);

        ProfileReport::create([
            'category' => $context->category,
            'context'  => $context->toArray(),
            'xhprof'   => $report,
        ]);
    }
}
