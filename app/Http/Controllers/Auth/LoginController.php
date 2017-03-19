<?php

namespace Yap\Http\Controllers\Auth;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Yap\Foundation\Auth\Authenticable;
use Yap\Http\Controllers\Controller;
use Yap\Models\User;

class LoginController extends Controller
{
    use Authenticable;

    protected $redirectTo = 'home';

    public function showPage()
    {
        return view('auth.login');
    }


    public function login(Socialite $socialite)
    {
        return $socialite->driver('github')->redirect();
    }


    public function handle(User $user, Socialite $socialite)
    {
        $githubUser = $socialite->driver('github')->user();
        $user = $user->firstByGithubIdOrFail($githubUser->getId());

        if ($user->logginable()) {
            $this->grant($user, $githubUser->token);
        }

        return $this->response();
    }
}
