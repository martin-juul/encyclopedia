<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Facades\RequestId;
use App\Logging\Channel;
use Closure;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('logging.request.enable')) {
            $request->attributes->add(['request_id' => RequestId::get()]);
        }

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse $response
     */
    public function terminate($request, $response): void
    {
        if (!config('logging.request.enable')) {
            return;
        }

        \Log::channel(Channel::REQUESTS)->info('request', [
            'id'          => RequestId::get(),
            'client'      => $request->getClientIp(),
            'fingerprint' => $request->fingerprint(),
            'url'         => $request->url(),
            'headers'     => $request->headers,
            'status'      => $response->status(),
        ]);
    }
}
