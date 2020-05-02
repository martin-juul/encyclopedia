<?php

namespace App\Utilities\Filesystem\Exceptions;

use Throwable;

class FilesystemException extends \Exception
{
    protected ?string $path;

    public function __construct(
        string $message,
        ?string $path = null,
        $code = 0,
        Throwable $previous = null
    )
    {
        $this->path = $path;

        if ($this->path !== null) {
            $message .= ". Path: $path";
        }

        parent::__construct($message, $code, $previous);
    }
}
