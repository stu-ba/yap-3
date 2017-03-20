<?php

namespace Yap\Foundation\Auth;

use Yap\Models\User;

trait Authenticable
{

    protected $githubTokenCookie;


    protected function grant(User $user, string $token)
    {
        $this->setGithubTokenCookie($token);
        auth()->loginUsingId($user->id, true);
    }


    protected function setGithubTokenCookie(string $token)
    {
        $cookie = resolve('cookie');
        $this->githubTokenCookie = $cookie->forever('github_token', $token);
    }


    protected function response()
    {
        return redirect()->route($this->redirectTo ?? 'home')->cookie($this->githubTokenCookie ?? null);
    }
}