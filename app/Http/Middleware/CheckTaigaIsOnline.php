<?php

namespace Yap\Http\Middleware;

use Closure;
use Yap\Auxiliary\HttpCheckers\Taiga as TaigaChecker;
use Yap\Exceptions\TaigaOfflineException;

class CheckTaigaIsOnline
{

    /**
     * @var \Yap\Auxiliary\HttpCheckers\Taiga
     */
    protected $checker;


    /**
     * CheckTaigaIsOnline constructor.
     *
     * @param \Yap\Auxiliary\HttpCheckers\Taiga $checker
     */
    public function __construct(TaigaChecker $checker)
    {

        $this->checker = $checker;
    }


    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return mixed
     * @throws \Yap\Exceptions\TaigaOfflineException
     */
    public function handle($request, Closure $next)
    {
        if ( ! $this->checker->check()) {
            throw new TaigaOfflineException();
        }

        return $next($request);
    }
}
