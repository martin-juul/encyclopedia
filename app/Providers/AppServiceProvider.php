<?php

namespace App\Providers;

use App\Http\Middleware\ProfileRequest;
use App\Profiling\XHProf;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (config('profiling.enabled')) {
            $this->app->singleton(ProfileRequest::class);
            $this->app->singleton(XHProf::class);
        }
    }
}
