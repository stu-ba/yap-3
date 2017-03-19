<?php

namespace Yap\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as StockAuthenticate;
use Yap\Exceptions\UserBannedException;

class Authenticate extends StockAuthenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string[]                 ...$guards
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($guards);
        $this->banned();

        return $next($request);
    }


    /**
     * Determine if the user is banned.
     * @return void
     * @throws UserBannedException
     */
    protected function banned()
    {
        if ($this->auth->user()->is_banned) {
            throw new UserBannedException();
        }
    }
}