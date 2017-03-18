<?php

namespace Yap\Http\Controllers\Auth;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Yap\Http\Controllers\Controller;
use Yap\Models\Invitation;
use Yap\User;

class LoginController extends Controller
{

    public function redirectToGithub(Socialite $socialite)
    {
        return $socialite->driver('github')->redirect();
    }

    public function handleGithubCallback(string $token = null, Invitation $invitation, Socialite $socialite)
    {
        $user = $socialite->driver('github')->user();
        dd($user, $token, decrypt($token), $invitation->isTokenValid($token), $invitation->isTokenValid(decrypt($token)));

        if ($token === null) {
            //trying to login
        } else {
            //trying to login for first time
            $token = decrypt($token);
            if ($invitation->isTokenValid($token)) {

            }
        }

        return redirect()->route('home');
    }
}
