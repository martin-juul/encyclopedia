<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Logging\Channel;
use App\Utilities\Extensions\ArrExt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

abstract class AbstractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected function debug(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    protected function info(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    protected function notice(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    protected function warning(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    protected function error(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    protected function errorE(string $message, \Throwable $e)
    {
        $this->error($message, [
            'exception' => [
                'code'    => $e->getCode(),
                'line'    => $e->getLine(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    protected function alert(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    protected function critical(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    protected function emergency(string $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        $context += ['job' => static::class];
        $context = ArrExt::dotKeys($context);

        $channel = Channel::JOBS;
        if (in_array($level, ['debug', 'info', 'notice'])) {
            // Don't log non errors to file
            $channel = Channel::STDOUT;
        } else {
            // Also log errors to console
            \Log::channel(CHANNEL::STDOUT)->log($level, $message, $context);
        }

        \Log::channel($channel)->log($level, $message, $context);
    }
}
