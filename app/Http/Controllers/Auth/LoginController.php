<?php

namespace Yap\Http\Controllers\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Yap\Foundation\Auth\Authenticable;
use Yap\Foundation\Auth\UserRegistrar;
use Yap\Http\Controllers\Controller;
use Yap\Models\User;

class LoginController extends Controller
{
    use Authenticable;

    protected $redirectTo = 'home';


    /**
     * Show login page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPage()
    {
        return view('auth.login');
    }


    /**
     * Redirect to GitHub authorization server.
     *
     * @param Socialite $socialite
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function login(Socialite $socialite)
    {
        return $socialite->driver('github')->redirect();
    }


    /**
     * Handle GitHub callback redirect.
     *
     * @param User          $user
     * @param Socialite     $socialite
     *
     * @param UserRegistrar $registrar
     *
     * @return $this
     */
    public function handle(User $user, Socialite $socialite, UserRegistrar $registrar)
    {
        $githubUser = $socialite->driver('github')->user();

        try {
            $user = $user->whereGithubId($githubUser->getId())->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $user = $registrar->register($githubUser);
        }

        $this->attempt($user);
        $this->setGithubTokenCookie($githubUser->token);

        return $this->response();
    }
}
