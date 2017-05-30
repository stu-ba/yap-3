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
        $this->user       = $user;
    }


    public function register(...$args): User
    {
        if (func_num_args() === 1 && is_a($args[0], GithubUser::class)) {
            $this->githubUser   = $args[0];
            $this->userByGithub = null;

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
            $this->invitation->load([
                'user' => function ($q) {
                    $q->select(['id', 'email']);
                },
            ]);

            return $this->registerByInvitation();
        }

        throw new InvalidArgumentException('Bad number of arguments.');
    }


    private function registerByGithubUser(): User
    {
        $this->invitation = $this->invitation->with([
            'user' => function ($q) {
                $q->select(['id', 'email']);
            },
        ])->whereEmail($this->githubUser->getEmail())->first();

        if (is_null($this->invitation) && is_null($this->userByGithub)) {
            return $this->user->create($this->githubUserData());
        } elseif ( ! is_null($this->invitation)) {
            return $this->createByInvitation();
        }

        return $this->userByGithub;
    }


    /**
     * Format Github user data to array.
     *
     * @return array
     */
    private function githubUserData(): array
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

        if ( ! $this->invitation->isDepleted()) {
            $user->confirm();
            $this->invitation->deplete();
        }

        return $user;
    }


    private function registerByInvitation(): User
    {
        if ( ! is_null($this->userByGithub)) {
            if ($this->userByGithub->is_confirmed) {
                if (is_null($this->invitation->depleted_at)) {
                    $this->invitation->swapUser($this->userByGithub)->deplete();
                }

                return $this->userByGithub;
            } elseif (is_null($this->invitation->user->email) && ! $this->invitation->isDepleted()) {
                $this->invitation = $this->invitation->swapUser($this->userByGithub)->fresh('user');

                return $this->createByInvitation();
            } elseif ($this->invitation->user->email !== $this->githubUser->email) {
                return $this->userByGithub;
            }
        } elseif ($this->invitation->isDepleted() || ! is_null($this->invitation->user->email)) {
            return $this->registerByGithubUser();
        }

        return $this->createByInvitation();
    }
}
