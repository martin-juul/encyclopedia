<?php

namespace App\Utilities\Filesystem\Exceptions;

use Throwable;

class InvalidPathException extends FilesystemException
{
    public function __construct(string $path, $message = 'Invalid path', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $path, $code, $previous);
    }
}
