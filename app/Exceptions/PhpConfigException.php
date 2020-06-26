<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class PhpConfigException extends Exception
{
    public function __construct(string $key, $value)
    {
        $value = (string)$value;
        $message = "Unable to set {$key} to {$value}";

        parent::__construct($message);
    }
}
