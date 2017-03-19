<?php

namespace Yap\Foundation\Auth;

use Laravel\Socialite\Two\User as GithubUser;
use Yap\Models\User;

trait Authenticable {

    protected $githubTokenCookie;

    protected function grant(User $user, string $token) {
        $cookie = resolve('cookie');
        $this->githubTokenCookie = $cookie->forever('github_token', $token);
        auth()->loginUsingId($user->id, true);
    }

    protected function response()
    {
        return redirect()->route($this->redirectTo ?? 'home')->cookie($this->githubTokenCookie ?? null);
    }
}