<?php

namespace Yap\Http\Controllers\Auth;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Yap\Foundation\Auth\Authenticable;
use Yap\Foundation\Auth\UserRegistrar;
use Yap\Http\Controllers\Controller;
use Yap\Models\User;

class LoginController extends Controller
{

    use Authenticable;

    protected $redirectTo = 'home';

    protected $user;

    protected $registrar;


    public function __construct(User $user, UserRegistrar $registrar)
    {
        $this->user = $user;
        $this->registrar = $registrar;
    }


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
    public function redirect(Socialite $socialite)
    {
        return $socialite->driver('github')->redirect();
    }


    /**
     * Handle GitHub callback redirect.
     *
     * @param Socialite $socialite
     *
     * @return $this
     * @internal param User $user
     * @internal param UserRegistrar $registrar
     */
    public function handle(Socialite $socialite)
    {
        /** @var \Laravel\Socialite\Two\User $githubUser */
        $githubUser = $socialite->driver('github')->user();

        $this->login($githubUser);

        return $this->response();
    }
}
