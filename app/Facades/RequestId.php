<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string get()
 *
 * @see \App\Services\Http\RequestIdService
 */
class RequestId extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return static::class;
    }
}
