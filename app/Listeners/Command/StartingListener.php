<?php

namespace App\Listeners\Command;

use App\Profiling\XHProf;
use Illuminate\Console\Events\CommandStarting;

class StartingListener
{
    private XHProf $xhprof;

    public function __construct(XHProf $XHProf)
    {
        $this->xhprof = $XHProf;
    }

    /**
     * Handle the event.
     *
     * @param \Illuminate\Console\Events\CommandStarting $event
     *
     * @return void
     */
    public function handle(CommandStarting $event): void
    {
        if (!config('profiling.enabled')) {
            return;
        }

        $this->xhprof->start();
    }
}
