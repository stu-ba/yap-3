<?php

namespace Yap\Http\Middleware;

use Closure;
use Yap\Auxiliary\HttpCheckers\Github as GithubChecker;
use Yap\Exceptions\GithubOfflineException;

class CheckGithubIsOnline
{

    /**
     * @var \Yap\Auxiliary\HttpCheckers\Taiga
     */
    protected $checker;


    public function __construct(GithubChecker $checker)
    {

        $this->checker = $checker;
    }


    /**
     * @param          $request
     * @param \Closure $next
     *
     * @param string   $action
     *
     * @return mixed
     * @throws \Yap\Exceptions\GithubOfflineException
     */
    public function handle($request, Closure $next, $action = 'throw')
    {
        if ( ! $this->checker->check()) {
            switch ($action) {
                case 'alert': {
                    alert('warning', 'Github seems to be offline, you will not be able to save your progress.');
                    break;
                }
                default: {
                    throw new GithubOfflineException();
                }
            }
        }

        return $next($request);
    }
}
