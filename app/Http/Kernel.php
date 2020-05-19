<?php
declare(strict_types=1);

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\RequestLogger::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'dashboard' => [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ],

        'api' => [
            'json',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'               => \App\Http\Middleware\Authenticate::class,
        'auth.basic'         => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'           => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.response'     => \Spatie\ResponseCache\Middlewares\CacheResponse::class,
        'cache.headers'      => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'                => \Illuminate\Auth\Middleware\Authorize::class,
        'doNotCacheResponse' => \Spatie\ResponseCache\Middlewares\DoNotCacheResponse::class,
        'guest'              => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'json'               => \App\Http\Middleware\ForceJsonResponse::class,
        'password.confirm'   => \Illuminate\Auth\Middleware\RequirePassword::class,
        'profile'            => \App\Http\Middleware\ProfileRequest::class,
        'signed'             => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'           => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'           => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
