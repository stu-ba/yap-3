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

    /** @var User $userByGithub */
    private $userByGithub;


    public function __construct(Invitation $invitation, User $user)
    {
        $this->invitation = $invitation;
        $this->user = $user;
    }


    public function register(...$args): User
    {
        if (func_num_args() === 1 && is_a($args[0], GithubUser::class)) {
            $this->githubUser = $args[0];

            $this->userByGithub = $this->user->whereGithubId($this->githubUser->getId())->first();

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

            $this->userByGithub = $this->user->whereGithubId($this->githubUser->getId())->first();

            return $this->registerByInvitation();
        }

        throw new InvalidArgumentException('Bad number of arguments.');
    }


    private function registerByGithubUser(): User
    {
        $this->invitation = $this->invitation->whereEmail($this->githubUser->getEmail())->first();
        if ($this->invitation === null) {
            if ($this->userByGithub === null) {
                return $this->user->create($this->githubUserData());
            }

            return $user->syncWith($this->githubUserData());
        }

        return $this->createByInvitation();
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


    private function createByInvitation(): User
    {
        $user = $this->invitation->user;

        $user->syncWith($this->githubUserData());

        if ($this->invitation->isTokenValid()) {
            $user->confirm();
            $this->invitation->deplete();
        } else {
            $user->unconfirm();
            $this->invitation->deplete();
        }

        return $user;
    }


    private function registerByInvitation(): User
    {
        if (! is_null($this->userByGithub) && is_null($this->invitation->user->email) && $this->invitation->isTokenValid()) {
            if (!is_null($this->userByGithub->invitation)) {
                return $this->userByGithub;
            }
            $this->swapUsers($this->userByGithub);
            return $this->createByInvitation();
        } elseif (! is_null($this->userByGithub) && $this->invitation->user->email !== $this->githubUser->email) {
            return $this->userByGithub;
        } elseif ((is_null($this->userByGithub) && ! $this->invitation->isTokenValid()) || ! is_null($this->invitation->user->email)) {
            return $this->newInstance()->registerByGithubUser();
        }

        return $this->createByInvitation();
    }


    /**
     * @param $user
     *
     * @return mixed
     */
    private function swapUsers(User $user)
    {
        $userOld = $this->invitation->user;
        $this->invitation->user_id = $user->id;
        $this->invitation->save();
        $this->invitation = $this->invitation->fresh();
        $userOld->delete();
        return $user;
    }


    private function newInstance(): self
    {
        $this->invitation = $this->invitation->newInstance();
        $this->user = $this->user->newInstance();

        return $this;
    }
}
