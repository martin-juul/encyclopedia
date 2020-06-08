<?php
declare(strict_types=1);

namespace App\Utilities;

use Ds\Map;

class FlushableMap
{
    private int $max;
    private Map $items;
    private $cb;

    public function __construct(int $max, callable $flushFn)
    {
        $this->max = $max;
        $this->items = new Map;
        $this->cb = $flushFn;
    }

    public function set($key, $value)
    {
        if ($this->check()) {
            $this->flush();
        }

        $this->items->put($key, $value);
    }

    public function flush(): int
    {
        $count = $this->items->count();

        call_user_func($this->cb, $this->items);

        $this->items->clear();

        return $count;
    }

    private function check(): bool
    {
        return $this->items->count() > $this->max;
    }
}
