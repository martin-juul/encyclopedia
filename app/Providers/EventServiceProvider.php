<?php
declare(strict_types=1);

namespace App\Providers;

use App\Listeners\Command\FinishedListener;
use App\Listeners\Command\StartingListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class      => [
            SendEmailVerificationNotification::class,
        ],
        CommandStarting::class => [
            StartingListener::class,
        ],
        CommandFinished::class => [
            FinishedListener::class,
        ],
    ];
}
