<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\Invitation[] $invitations
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User banned($value = true)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User colleagues(\Yap\Models\User $user = null)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User filled()
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User filter($filterName = 'all')
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User isAdmin($value = true)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User sortable($defaultSortParameters = null)
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
    use Notifiable, Sortable;

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

    public $sortable = [
        'id',
        'name',
        'email',
        'username',
        'created_at',
        'updated_at'
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

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFilled(Builder $query): Builder
    {
        return $query->whereNotNull('email')->where('github_id', '<>', 0);
    }


    public function scopeFilter(Builder $query, string $filterName = null): Builder
    {
        switch ($filterName) {
            case 'banned':
                return $query->banned();
            case 'colleagues':
                return $query->colleagues(auth()->user());
            case 'admins':
                return $query->isAdmin();
            default:
                return $query->banned(false);
        }
    }


    public function scopeBanned(Builder $query, bool $value = true): Builder
    {
        return $query->whereIsBanned($value);
    }

    public function scopeIsAdmin(Builder $query, bool $value = true): Builder
    {
        return $query->whereIsAdmin($value);
    }

    public function scopeColleagues(Builder $query, User $user = null): Builder
    {
        d($user ?? 'unset');
        return $query;
    }

    /**
     * Get system User instance.
     *
     * @return array|\Illuminate\Database\Eloquent\Model|null|\stdClass|static
     */
    public function system()
    {
        return $this->whereGithubId(0)->whereIsAdmin(true)->first();
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

    protected function swapNotifications(self $user): void
    {
        $this->notifications()->update(['notifiable_id' => $user->id]);
    }

    public function swapWith(self $user): self
    {
        if (is_null($user->email) && ! $user->is_confirmed) {
            //swapping every associated model except invitation
            $user->swapNotifications($this);
            //swap project
            //swap ...
            $user->delete();
        } else {
            $this->setRawAttributes(array_except($user->attributes, 'id'));
            $user->delete();
            $this->save();
        }

        return $this;
    }
}
