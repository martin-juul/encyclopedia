<?php
declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null get()
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
