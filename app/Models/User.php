<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Notifiable;
use Laravel\Socialite\Two\User as GithubUser;
use Yap\Exceptions\UserBannedException;
use Yap\Exceptions\UserNotConfirmedException;
use Yap\Foundation\Auth\User as Authenticatable;

/**
 * Yap\Models\User
 *
 * @property int $id
 * @property int $taiga_id
 * @property int $github_id
 * @property string $email
 * @property string $username
 * @property string $name
 * @property string $bio
 * @property string $ban_reason
 * @property string $avatar
 * @property string $remember_token
 * @property bool $is_admin
 * @property bool $is_banned
 * @property bool $is_confirmed
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereBanReason($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereBio($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereGithubId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereIsBanned($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereIsConfirmed($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereTaigaId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'taiga_id',
        'github_id',
        'email',
        'username',
        'name',
        'bio',
        'ban_reason',
        'avatar',
        'is_admin',
        'is_banned',
        'is_confirmed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'taiga_id',
        'github_id',
        'remember_token',
    ];

    protected $casts = [
        'taiga_id'     => 'int',
        'github_id'    => 'int',
        'is_admin'     => 'boolean',
        'is_banned'    => 'boolean',
        'is_confirmed' => 'boolean',
    ];

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }


    public function byGithubUserOrCreate(GithubUser $githubUser): self
    {
        try {
            $user = $this->whereGithubId($githubUser->getId())->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $user = $this->newInstance()->create($this->githubUserData($githubUser));
        }

        return $user;
    }


    /**
     * Get system User instance.
     * @return array|\Illuminate\Database\Eloquent\Model|null|\stdClass|static
     */
    public function system()
    {
        return $this->whereGithubId(0)->whereTaigaId(0)->whereIsAdmin(true)->first();
    }


    public function logginable(): bool
    {
        if ($this->is_banned) {
            throw new UserBannedException();
        }

        if ( ! $this->is_confirmed) {
            throw new UserNotConfirmedException();
        }

        return true;
    }


    public function confirm(): self
    {
        $this->is_confirmed = true;
        $this->save();

        return $this;
    }


    public function unconfirm(): self
    {
        $this->is_confirmed = false;
        $this->save();

        return $this;
    }


    public function unban(): self
    {
        $this->is_banned = false;
        $this->ban_reason = null;
        $this->save();

        return $this;
    }


    public function ban(string $reason): self
    {
        $this->is_banned = true;
        $this->ban_reason = str_limit($reason, 250, '...');
        $this->save();

        return $this;
    }


    /**
     * Synchronize User with GitHub data.
     *
     * @param GithubUser $user
     *
     * @return User
     */
    public function syncWith(GithubUser $user): self
    {
        $this->update($this->githubUserData($user));
        return $this;
    }


    /**
     * Format Github user data to array.
     *
     * @param GithubUser $user
     *
     * @return array
     */
    protected function githubUserData(GithubUser $user): array
    {
        return [
            'github_id' => $user->getId(),
            'email'     => $user->getEmail(),
            'username'  => $user->getNickname(),
            'name'      => $user->getName(),
            'avatar'    => $user->getAvatar(),
            'bio'       => $user->user['bio'],
        ];
    }
}
