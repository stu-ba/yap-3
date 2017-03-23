<?php

namespace Yap\Http\Controllers\Auth;

use Yap\Http\Controllers\Controller;
use Yap\User;

class LogoutController extends Controller
{
    public function logout()
    {
        //redirect to taiga /
        // logout from taiga /
        // redirect back here if referrer is taiga logout and return to login page
        auth()->logout();

        return redirect()->route('login');
    }
}
