<?php

namespace Yap\Http\Controllers\Auth;

use Yap\Auxiliary\HttpCheckers\Taiga;
use Yap\Http\Controllers\Controller;

class LogoutController extends Controller
{

    public function logout($token = null, \Illuminate\Cookie\CookieJar $cookie, Taiga $checker)
    {
        $cookie->queue($cookie->forget('github_token'));
        auth()->logout();

        if (is_null($token) && $checker->check()) {
            return redirect()->away(config('yap.taiga.site').'logout');
        }

        return redirect()->route('login');
    }
}
