<?php

namespace Yap\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check() && ! $this->isTaigaLogin($request)) {
            return redirect()->route('profile');
        }

        return $next($request);
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    private function isTaigaLogin(Request $request)
    {
        return $this->isTaigaReferer($request->headers->get('referer', null)) && $request->route()
                                                                                         ->getName() === 'login.taiga';
    }


    /**
     * @param null|string $referer
     *
     * @return bool
     */
    private function isTaigaReferer(?string $referer = null)
    {
        if (is_null($referer)) {
            return false;
        }

        return str_contains(config('yap.taiga.site'), parse_url($referer, PHP_URL_HOST));
    }
}
