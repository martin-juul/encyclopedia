<?php

namespace App\Providers;

use App\Facades\RequestId;
use App\Services\Http\RequestIdService;
use Illuminate\Support\ServiceProvider;

class RequestIdProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(RequestId::class, static function () {
            return new RequestIdService;
        });
    }
}
