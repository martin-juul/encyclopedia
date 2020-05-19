<?php
declare(strict_types=1);

namespace App\Logging\Handlers;

use App\Facades\RequestId;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerInterface;

class AttachRequestId
{
    public function __invoke(LoggerInterface $logger)
    {
        if (!method_exists($logger, 'getHandlers') || app()->runningInConsole()) {
            return;
        }

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($this->getFormatter());
        }
    }

    protected function getFormatter(): LineFormatter
    {
        $id = RequestId::get();

        $format = str_replace('[%datetime%] ', "[%datetime%] {$id} ", LineFormatter::SIMPLE_FORMAT);

        return new LineFormatter($format, null, true, true);
    }
}
