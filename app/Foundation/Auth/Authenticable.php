<?php

namespace Yap\Foundation\Auth;

use Yap\Models\User;

trait Authenticable
{

    protected $githubTokenCookie;

    /**
     * Grant user log in.
     * @param User   $user
     */
    public function grant(User $user): void
    {
        auth()->loginUsingId($user->id, true);
    }




    /**
     * Attempt to log in user
     * @param User       $user
     */
    public function attempt(User $user): void
    {
        if ($user->logginable()) {
            $this->grant($user);
        }
    }


    public function setGithubTokenCookie(string $token): void
    {
        $cookie = resolve('cookie');
        $this->githubTokenCookie = $cookie->forever('github_token', $token);
    }


    public function response()
    {
        return redirect()->route($this->redirectTo ?? 'home')->cookie($this->githubTokenCookie);
    }
}
