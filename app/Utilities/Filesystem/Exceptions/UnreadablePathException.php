<?php

namespace App\Utilities\Filesystem\Exceptions;

use Throwable;

class UnreadablePathException extends FilesystemException
{
    public function __construct(string $path, $code = 0, Throwable $previous = null)
    {
        parent::__construct('The given location is not readable', $path, $code, $previous);
    }
}
