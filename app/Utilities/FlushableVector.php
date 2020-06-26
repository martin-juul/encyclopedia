<?php
declare(strict_types=1);

namespace App\Utilities;

use Ds\Vector;

class FlushableVector
{
    private int $max;
    private Vector $items;
    private $cb;

    public function __construct(int $max, callable $flushFn)
    {
        $this->max = $max;
        $this->items = new Vector;
        $this->cb = $flushFn;
    }

    public function push($item): void
    {
        if ($this->check()) {
            $this->flush();
        }

        $this->items->push($item);
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
