<?php
declare(strict_types=1);

namespace App\Exceptions\Profiling;

class XHProfExtensionMissing extends \RuntimeException
{

    public function __construct($code = 0, \Throwable $previous = null)
    {
        $message = 'xhprof must be installed and loaded. Install it by running: pecl install xhprof';
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report(): void
    {
        \Log::emergency('xhprof extension missing', [
            'explanation' => 'Profiling relies on the xhprof php extension. This error is triggered as you have enabled the feature, while missing the extension.',
            'solution'    => 'Install by running: pecl install xhprof and/or enable it in php.ini (find the location by running: php --ini).',
        ]);
    }
}
