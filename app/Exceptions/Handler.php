<?php

namespace Yap\Exceptions;

use Exception;
use Github\Exception\RuntimeException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Kyslik\ColumnSortable\Exceptions\ColumnSortableException;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \Yap\Exceptions\InvitationRegistrarException::class,
        \Yap\Exceptions\UserBannedException::class,
        \Yap\Exceptions\UserNotConfirmedException::class,
        \Github\Exception\RuntimeException::class,
    ];


    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @throws Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof UserBannedException) {
            return $this->banned($request, $exception);
        } elseif ($exception instanceof UserNotConfirmedException) {
            return abort(403);
        } elseif ($exception instanceof DecryptException) {
            return abort(400);
        } elseif ($exception instanceof InvalidStateException) {
            return redirect()->guest(route('login'));
        } elseif ($exception instanceof ColumnSortableException) {
            alert('warning', 'Your actions were logged. Try not to play with URL in future.');

            return redirect()->route('profile');
        } elseif ($exception instanceof TooManyRequestsHttpException) {
            return abort($exception->getStatusCode(), '', $exception->getHeaders());
        } elseif ($exception instanceof TaigaOfflineException) {
            if (\App::runningInConsole()) {
                throw $exception;
            }
            alert('warning', 'Taiga instance seems to be offline, please try later.');

            return redirect()->back();
        } elseif ($exception instanceof GithubOfflineException) {
            if (\App::runningInConsole()) {
                throw $exception;
            }
            alert('warning', 'GitHub seems to be offline, please try later.');

            return redirect()->back();
        } elseif ($exception instanceof RuntimeException) {
            alert('warning', 'Requested repository seems to be non-existent.');

            return redirect()->back();
        } elseif ($exception instanceof AuthorizationException) {
            if ($request->isXmlHttpRequest() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage()], 403);
            }
            throw $exception;
        }

        return parent::render($request, $exception);
    }


    protected function banned($request, UserBannedException $exception)
    {
        if (auth()->check()) {
            return redirect()->route('logout');
        }

        return abort(403, $exception->getMessage());
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }


    /**
     * Prepare response containing exception render.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, Exception $e)
    {
        if ( ! $this->isHttpException($e) && config('app.debug')) {
            return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);
        }

        if ( ! $this->isHttpException($e)) {
            $e = new HttpException(500, $e->getMessage());
        }

        return $this->toIlluminateResponse($this->renderHttpException($e), $e);
    }
}
