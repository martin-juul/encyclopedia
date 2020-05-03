<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ForceJsonResponse
{
    /**
     * Ensures the response content is always json
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return JsonResponse
     */
    public function handle($request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $res */
        $res = $next($request);

        if (!$res instanceof JsonResponse) {
            $res = new JsonResponse(
                $res->getOriginalContent(),
                $res->getStatusCode(),
                $res->headers,
                config('app.encoding.json')
            );
        }

        return $res;
    }
}
