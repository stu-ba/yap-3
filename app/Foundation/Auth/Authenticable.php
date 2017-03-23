<?php

namespace Yap\Foundation\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Yap\Models\User;

trait Authenticable
{

    protected $githubTokenCookie;


    public function response()
    {
        return redirect()->route($this->redirectTo ?? 'home')->cookie($this->githubTokenCookie);
    }


    /**
     * @param      $githubUser
     *
     * @internal param User $user
     */
    private function login($githubUser): void
    {
        try {
            $user = $this->user->whereGithubId($githubUser->getId())->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $user = $this->registrar->register($githubUser);
        }

        $this->attempt($user);
        $this->setGithubTokenCookie($githubUser->token);
    }


    /**
     * Attempt to log in user
     *
     * @param User $user
     */
    public function attempt(User $user): void
    {
        if ($user->logginable()) {
            auth()->loginUsingId($user->id, true);
        }
    }


    public function setGithubTokenCookie(string $token): void
    {
        $cookie = resolve('cookie');
        $this->githubTokenCookie = $cookie->forever('github_token', $token);
    }


    /**
     * @param $invitation
     * @param $githubUser
     */
    private function register($invitation, $githubUser): void
    {
        $user = $this->registrar->register($invitation, $githubUser);

        $this->attempt($user);
        $this->setGithubTokenCookie($githubUser->token);
    }
}
