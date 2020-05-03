<?php

namespace App\Http\Middleware;

use App\Facades\RequestId;
use App\Logging\Channel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $request->attributes->add(['request_id' => RequestId::getId()]);

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        \Log::channel(Channel::REQUESTS)->info('request', [
            'client'      => $request->getClientIp(),
            'fingerprint' => $request->fingerprint(),
            'url'         => $request->url(),
            'headers'     => $request->headers,
            'status'      => $response->status(),
        ]);
    }
}
