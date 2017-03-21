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
     * @param User      $user
     * @param Socialite $socialite
     *
     * @return $this
     */
    public function handle(User $user, Socialite $socialite)
    {
        $githubUser = $socialite->driver('github')->user();


        //see does email exist in
        $user = $user->byGithubUserOrCreate($githubUser);

        $this->attempt($user, $githubUser);
        $this->setGithubTokenCookie($githubUser->token);

        return $this->response();
    }
}
