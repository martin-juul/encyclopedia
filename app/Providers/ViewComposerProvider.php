<?php

namespace App\Providers;

use App\Http\Composers\RequestIdComposer;
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
    }
}
