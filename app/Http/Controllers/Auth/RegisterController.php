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
    private $invitation;


    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }


    public function register(string $token, Socialite $socialite)
    {
        $invitation = $this->invitation->whereToken($token)->firstOrFail();

        if (! $invitation->isDepleted()) {
            $redirect_uri = config('services.github.redirect').'/'.encrypt($token);

            return $socialite->driver('github')->with(['redirect_uri' => $redirect_uri])->scopes(['user:email'])->redirect();
        }
        //todo: maybe inform user that token has expired or been used
        return redirect()->route('login');
    }

    public function handle(string $encryptedToken, Socialite $socialite, UserRegistrar $registrar)
    {
        $invitation = $this->invitation->whereToken(decrypt($encryptedToken))->firstOrFail();
        $githubUser = $socialite->driver('github')->user();

        $user = $registrar->register($invitation, $githubUser);

        $this->attempt($user);
        $this->setGithubTokenCookie($githubUser->token);

        return $this->response();
    }
}
