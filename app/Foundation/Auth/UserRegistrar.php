<?php

namespace Yap\Foundation\Auth;

use InvalidArgumentException;
use Laravel\Socialite\Two\User as GithubUser;
use Yap\Models\Invitation;
use Yap\Models\User;

class UserRegistrar
{

    /** @var Invitation $invitation */
    protected $invitation;

    /** @var GithubUser $githubUser */
    protected $githubUser;

    /** @var User $user */
    private $user;


    function __construct(Invitation $invitation, User $user)
    {
        $this->invitation = $invitation;
        $this->user = $user;
    }


    public function register(...$args): User
    {
        if (func_num_args() === 1 && is_a($args[0], GithubUser::class)) {
            $this->githubUser = $args[0];

            return $this->registerByGithubUser();
        } elseif (func_num_args() === 2) {
            if (is_a($args[0], Invitation::class) && is_a($args[1], GithubUser::class)) {
                $this->invitation = $args[0];
                $this->githubUser = $args[1];
            } elseif (is_a($args[0], GithubUser::class) && is_a($args[1], Invitation::class)) {
                $this->invitation = $args[1];
                $this->githubUser = $args[0];
            } else {
                throw new InvalidArgumentException('Unrecognized type of argument.');
            }

            return $this->registerByInvitation();
        }

        throw new InvalidArgumentException('Bad number of arguments.');
    }


    private function registerByGithubUser(): User
    {
        $this->invitation = $this->invitation->whereEmail($this->githubUser->email)->first();

        if ($this->invitation === null) {
            return $this->user->create($this->githubUserData());
        }

        return $this->registerByInvitation();
    }


    /**
     * Format Github user data to array.
     *
     * @return array
     */
    protected function githubUserData(): array
    {
        return [
            'github_id' => $this->githubUser->getId(),
            'email'     => $this->githubUser->getEmail(),
            'username'  => $this->githubUser->getNickname(),
            'name'      => $this->githubUser->getName(),
            'avatar'    => $this->githubUser->getAvatar(),
            'bio'       => $this->githubUser->user['bio'],
        ];
    }


    private function registerByInvitation(): User
    {
        $this->user = $this->invitation->user;
        $this->user->syncWith($this->githubUserData());

        if ($this->invitation->isTokenValid()) {
            $this->user->confirm();
            $this->invitation->deplete();
        } else {
            $this->user->unconfirm();
            $this->invitation->deplete();
        }

        return $this->user;
    }

}