<?php

namespace Yap\Models;

use Illuminate\Notifications\Notifiable;
use Yap\Events\UserDemoted;
use Yap\Events\UserPromoted;
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
 * @property-read \Yap\Models\Invitation $invitation
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
        'is_confirmed',
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
        'taiga_id' => 'int',
        'github_id' => 'int',
        'is_admin' => 'boolean',
        'is_banned' => 'boolean',
        'is_confirmed' => 'boolean',
    ];

    /**
     * Boot function for using with User Events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->attributes['github_id'] = $model->getOriginal('github_id') ?? $model->attributes['github_id'] ?? null;
        });
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }

    /**
     * Get system User instance.
     *
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
        if (! $this->is_confirmed) {
            $this->is_confirmed = true;
            $this->save();
            //TODO: add event that is fired when confirmed, set up taiga / set up github etc
        }

        return $this;
    }

    public function promote(): self
    {
        if (! $this->is_admin) {
            $this->is_admin = true;
            $this->save();

            if (! is_null($this->github_id)) {
                event(new UserPromoted($this));
            }
        }

        return $this;
    }

    public function demote(): self
    {
        if ($this->is_admin) {
            $this->is_admin = false;
            $this->save();

            if (! is_null($this->github_id)) {
                event(new UserDemoted($this));
            }
        }

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
     *
     * @internal param GithubUser $user
     */
    public function syncWith(array $githubUserData): self
    {
        $this->update($githubUserData);

        return $this;
    }

    public function swapWith(self $user): self
    {
        $this->setRawAttributes(array_except($user->attributes, 'id'));
        $user->delete();
        $this->save();

        return $this;
    }
}
