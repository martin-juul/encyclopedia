<?php
declare(strict_types=1);

namespace App\Providers;

use App\Http\Composers\RequestIdComposer;
use App\Http\Composers\UserComposer;
use Illuminate\Support\ServiceProvider;
use View;

class ViewComposerProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        View::composer('*', RequestIdComposer::class);
        View::composer(['dashboard.partials.sidebar'], UserComposer::class);
    }
}
