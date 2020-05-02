<?php

namespace App\Jobs\Traits;

use App\Logging\Channel;
use App\Utilities\Extensions\ArrExt;
use Illuminate\Support\Facades\Log;

trait Logging
{
    public function debug(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    public function info(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    public function notice(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    public function warning(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    public function error(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    public function alert(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    public function critical(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    public function emergency(string $message, array $context = [], string $class = __CLASS__, string $method = __FUNCTION__): void
    {
        $this->log(__FUNCTION__, $message, $class, $method, $context);
    }

    private function log(string $level, string $message, string $class, string $method, array $context = []): void
    {
        $context += [
            'job' => [
                'classFQN' => $class,
                'method'   => $method,
            ],
        ];

        Log::channel(Channel::JOBS)->log($level, $message, ArrExt::dotKeys($context));
    }
}
