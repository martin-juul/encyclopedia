<?php
declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\ProfileRequest;
use App\Pagination\LengthAwarePaginator;
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

        $this->app->bind(LengthAwarePaginator::class);
    }
}
