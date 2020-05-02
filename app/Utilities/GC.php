<?php

namespace App\Utilities;

class GC
{
    public static function collectCycles(): int
    {
        return gc_collect_cycles();
    }

    public static function flushMemoryCaches(): int
    {
        return gc_mem_caches();
    }

    public static function flush(): void
    {
        static::collectCycles();
        static::flushMemoryCaches();
    }
}
