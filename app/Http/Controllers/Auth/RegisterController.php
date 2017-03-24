<?php

namespace Yap\Http\Controllers\Auth;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Yap\Foundation\Auth\Authenticable;
use Yap\Foundation\Auth\UserRegistrar;
use Yap\Http\Controllers\Controller;
use Yap\Models\Invitation;

//use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{

    use Authenticable;

    protected $redirectTo = 'home';

    /**
     * @var Invitation
     */
    protected $invitation;

    /**
     * @var UserRegistrar
     */
    protected $registrar;


    public function __construct(Invitation $invitation, UserRegistrar $registrar)
    {
        $this->invitation = $invitation;
        $this->registrar = $registrar;
    }


    public function redirect(string $token, Socialite $socialite)
    {
        $invitation = $this->invitation->whereToken($token)->firstOrFail();

        if ( ! $invitation->isDepleted()) {
            $redirect_uri = config('services.github.redirect').'/'.encrypt($token);

            return $socialite->driver('github')->with(['redirect_uri' => $redirect_uri])->scopes(['user:email', 'admin:org'])->redirect();
        }

        //todo: maybe inform user that token has expired or been used
        return redirect()->route('login');
    }


    public function handle(string $encryptedToken, Socialite $socialite)
    {
        $invitation = $this->invitation->whereToken(decrypt($encryptedToken))->firstOrFail();
        $githubUser = $socialite->driver('github')->user();

        $this->register($invitation, $githubUser);

        return $this->response();
    }
}
