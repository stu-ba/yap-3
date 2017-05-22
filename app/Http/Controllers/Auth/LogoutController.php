<?php

namespace Yap\Http\Controllers\Auth;

use Yap\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function logout($token = null, \Illuminate\Cookie\CookieJar $cookie)
    {
        $cookie->queue($cookie->forget('github_token'));
        auth()->logout();

        if (is_null($token)) {
            return redirect()->away(config('yap.taiga.site').'logout');
        }

        return redirect()->route('login');
    }
}
