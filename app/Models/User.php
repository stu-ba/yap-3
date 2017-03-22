<?php

namespace Yap\Models;

use Illuminate\Notifications\Notifiable;
use Yap\Exceptions\UserBannedException;
use Yap\Exceptions\UserNotConfirmedException;
use Yap\Foundation\Auth\User as Authenticatable;

/**
 * Yap\Models\User
 *
 * @property-read \Yap\Models\Invitation $invitation
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
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

        if (! $this->is_confirmed) {
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
     * @param array $githubUserData
     *
     * @return User
     * @internal param GithubUser $user
     *
     */
    public function syncWith(array $githubUserData): self
    {
        $this->update($githubUserData);

        return $this;
    }
}
