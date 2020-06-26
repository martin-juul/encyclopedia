<?php
declare(strict_types=1);

namespace App\Utilities;

use App\Exceptions\PhpConfigException;

class PhpConfig
{
    private $original = [];

    /**
     * @param string $key
     * @param $value
     *
     * @throws \App\Exceptions\PhpConfigException
     */
    public function set(string $key, $value): void
    {
        $res = ini_set($key, (string)$value);

        if ($res === false) {
            throw new PhpConfigException($key, $value);
        }

        $this->original[$key] = $res;
    }

    public function restore(): void
    {
        foreach ($this->original as $key => $value) {
            ini_restore($key);
        }
    }

    public function __destruct()
    {
        $this->restore();
    }
}
