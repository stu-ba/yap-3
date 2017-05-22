<?php

namespace Yap\Http\Middleware;

use Closure;

class OnlyXmlHttp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->isXmlHttpRequest()) {
            return redirect()->route('profile');
        }

        return $next($request);
    }
}
