<?php

namespace Yap\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests as StockThrottleRequests;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class ThrottleRequests extends StockThrottleRequests
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  int                      $maxAttempts
     * @param  float|int                $decayMinutes
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            if ($request->wantsJson() || $request->isXmlHttpRequest()) {
                return $this->buildResponse($key, $maxAttempts);
            }

            throw new TooManyRequestsHttpException($this->limiter->availableIn($key));
        }

        $this->limiter->hit($key, $decayMinutes);

        $response = $next($request);

        return $this->addHeaders($response, $maxAttempts, $this->calculateRemainingAttempts($key, $maxAttempts));
    }
}
