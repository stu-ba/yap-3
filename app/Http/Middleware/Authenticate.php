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
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string[]                 ...$guards
     *
     * @throws \Illuminate\Auth\AuthenticationException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($guards);
        $this->lastActive();

        if ( ! $request->is('auth/*')) {
            $this->banned();
        }

        return $next($request);
    }


    protected function lastActive()
    {
        resolve(\Illuminate\Cache\Repository::class)->remember('user-active-'.$this->auth->user()->id, 10, function () {
            return $this->auth->user()->touch();
        });
    }


    /**
     * Determine if the user is banned.
     *
     * @throws UserBannedException
     *
     * @return void
     */
    protected function banned()
    {
        if ($this->auth->user()->isBanned()) {
            throw new UserBannedException($this->auth->user()->ban_reason);
        }
    }
}
