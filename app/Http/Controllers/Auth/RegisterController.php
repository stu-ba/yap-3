<?php

namespace Yap\Http\Controllers\Auth;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Yap\Models\Invitation;
use Yap\Http\Controllers\Controller;
//use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    public function register(string $token, Invitation $invitation, Socialite $socialite)
    {
        if ($invitation->isTokenValid($token)) {
            $redirect_uri = config('services.github.redirect') . '/' . encrypt($token);
            return ($socialite->driver('github')->with(['redirect_uri' => $redirect_uri])->scopes(['user:email'])->redirect());
        }
        //token not valid
        return $token;
    }
}
