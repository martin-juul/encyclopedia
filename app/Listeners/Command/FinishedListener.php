<?php

namespace App\Listeners\Command;

use App\Models\Sys\Profile;
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
        if (!config('profiling.enabled')) {
            return;
        }

        $report = $this->xhprof->stop();
        $context = new CommandLineContext($event);

        Profile::create([
            'category' => $context->category,
            'context'  => $context->toArray(),
            'xhprof'   => $report,
        ]);
    }
}
