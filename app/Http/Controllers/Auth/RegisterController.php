<?php

namespace Yap\Http\Controllers\Auth;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Yap\Foundation\Auth\Authenticable;
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


    function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }


    public function register(string $token, Socialite $socialite)
    {
        $invitation = $this->invitation->whereToken($token)->firstOrFail();

        if ($invitation->isTokenValid()) {
            $redirect_uri = config('services.github.redirect').'/'.encrypt($token);

            return $socialite->driver('github')->with(['redirect_uri' => $redirect_uri])->scopes(['user:email'])->redirect();
        }
        //todo: maybe inform user that token has expired or been used
        return redirect()->route('login');
    }

    public function handle(string $encryptedToken, Socialite $socialite)
    {
        $invitation = $this->invitation->whereToken(decrypt($encryptedToken))->firstOrFail();

        //TODO: try to beautify
        if ($invitation->isTokenValid()) {

            $githubUser = $socialite->driver('github')->user();
            $user = $invitation->user;

            $user->syncWith($githubUser)->confirm();
            $this->grant($user);
            $this->setGithubTokenCookie($githubUser->token);
            $invitation->deplete();
        }

        return $this->response();
    }
}
