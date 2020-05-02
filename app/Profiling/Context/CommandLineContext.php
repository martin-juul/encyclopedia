<?php

namespace App\Profiling\Context;

use Illuminate\Console\Events\CommandFinished;

class CommandLineContext extends ProfileContext
{
    public string $category = 'cli';

    private CommandFinished $event;

    public function __construct(CommandFinished $event)
    {
        $this->event = $event;
    }

    public function toArray(): array
    {
        return [
            'command'   => $this->event->command,
            'args'      => is_null($this->event->input) ? null : $this->event->input->getArguments(),
            'exit_code' => $this->event->exitCode,
        ];
    }
}
