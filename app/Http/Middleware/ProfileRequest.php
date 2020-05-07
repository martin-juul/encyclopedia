<?php

namespace App\Http\Middleware;

use App\Models\Sys\ProfileReport;
use App\Profiling\Context\RequestContext;
use App\Profiling\XHProf;
use Closure;

class ProfileRequest
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param bool $sampleOnly
     *
     * @return mixed
     */
    public function handle($request, Closure $next, bool $sampleOnly = false)
    {
        if (config('profiling.enabled')) {
            $xhprof = app(XHProf::class);
            $xhprof->setSampleOnly($sampleOnly);
            $xhprof->start();
        }

        return $next($request);
    }

    public function terminate($request, $response): void
    {
        if (!config('profiling.enabled')) {
            return;
        }

        $xhprof = app(XHProf::class)->stop();
        $context = new RequestContext($request);

        ProfileReport::create([
            'category' => $context->category,
            'context'  => $context->toArray(),
            'xhprof'   => $xhprof,
        ]);
    }
}
