<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Builder;
use Kyslik\ColumnSortable\Sortable;
use Yap\Events\UserConfirmed;
use Yap\Events\UserDemoted;
use Yap\Events\UserPromoted;
use Yap\Exceptions\UserBannedException;
use Yap\Exceptions\UserNotConfirmedException;
use Yap\Foundation\Auth\User as Authenticatable;
use Yap\Foundation\Notifications\Notifiable;

/**
 * Yap\Models\User
 *
 * @property int
 *                   $id
 * @property int
 *                   $taiga_id
 * @property int
 *                   $github_id
 * @property string
 *                   $email
 * @property string
 *                   $username
 * @property string
 *                   $name
 * @property string
 *                   $bio
 * @property string
 *                   $ban_reason
 * @property string
 *                   $avatar
 * @property string
 *                   $remember_token
 * @property bool
 *                   $is_admin
 * @property bool
 *                   $is_confirmed
 * @property \Carbon\Carbon
 *                   $created_at
 * @property \Carbon\Carbon
 *                   $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\Invitation[]
 *                        $invitations
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Yap\Foundation\Notifications\DatabaseNotification[]
 *                $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[]
 *                    $parentnotifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\Project[]
 *                        $projects
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User banned($value = true)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User colleagues()
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User filled()
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User filter($filterName = null)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User isAdmin($value = true)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User leader()
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User participant()
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User sortable($defaultSortParameters = null)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereBanReason($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereBio($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereGithubId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereIsAdmin($value)
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

    public $sortable = [
        'id',
        'name',
        'email',
        'username',
        'created_at',
        'updated_at',
    ];

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
        'taiga_id'     => 'int',
        'github_id'    => 'int',
        'is_admin'     => 'boolean',
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
            $model->attributes['github_id'] =
                $model->getOriginal('github_id') ?? $model->attributes['github_id'] ?? null;
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


    public function scopeLeader($query)
    {
        return $query->wherePivot('is_leader', '=', '1');
    }


    public function scopeParticipant($query)
    {
        return $query->wherePivot('is_leader', '=', '0');
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFilled(Builder $query): Builder
    {
        return $query->whereNotNull('email');
    }


    public function scopeFilter(Builder $query, string $filterName = null): Builder
    {
        switch ($filterName) {
            case 'banned':
                return $query->banned();
            case 'colleagues':
                return $query->banned(false)->colleagues();
            case 'admins':
                return $query->isAdmin();
            default:
                return $query->banned(false);
        }
    }


    public function scopeBanned(Builder $query, bool $value = true): Builder
    {
        return $value ? $query->whereNotNull('ban_reason') : $query->whereNull('ban_reason');
    }


    public function scopeIsAdmin(Builder $query, bool $value = true): Builder
    {
        return $query->whereIsAdmin($value);
    }


    public function scopeColleagues(Builder $query): Builder
    {
        //TODO: hardcoded only for current user
        return $query->whereIn('id', auth()->user()->colleaguesIds());
    }


    public function colleaguesIds(): array
    {
        return $this->projects->load([
            'participants' => function ($query) {
                return $query->select('id');
            },
        ])->pluck('participants')->collapse()->unique('id')->whereNotIn('id', [$this->id])->pluck('id')->all();
    }


    /**
     * Get system User instance.
     *
     * @return array|\Illuminate\Database\Eloquent\Model|null|\stdClass|static
     */
    public function system()
    {
        return $this->whereGithubId(config('yap.github.id'))->whereIsAdmin(true)->first();
    }


    public function logginable(): bool
    {
        if ($this->isBanned()) {
            throw new UserBannedException();
        }

        if ( ! $this->is_confirmed) {
            throw new UserNotConfirmedException();
        }

        return true;
    }


    public function isBanned(): bool
    {
        return ! is_null($this->ban_reason);
    }


    public function confirm(): self
    {
        if ( ! $this->is_confirmed) {
            $this->is_confirmed = true;
            $this->save();
            event(new UserConfirmed($this));
            //TODO: do something to promote / demote
        }

        return $this;
    }


    public function promote($force = false): self
    {
        if (( ! $this->is_admin && ! $this->isBanned()) || $force) {
            $this->is_admin = true;
            $this->save();

            if ( ! is_null($this->github_id) && ! is_null($this->taiga_id)) {
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

            if ( ! is_null($this->github_id) && ! is_null($this->taiga_id)) {
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
        if ($this->isBanned()) {
            //TODO: user got unbanned event - add to teams etc.
        }

        $this->ban_reason = null;
        $this->save();

        return $this;
    }


    public function ban(string $reason): self
    {
        if ( ! $this->isBanned()) {
            //TODO: user got banned event - remove from teams etc.
        }

        $this->ban_reason = str_limit($reason, 254, '...');
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
        if (is_null($user->email) && ! $user->is_confirmed) {
            //swapping every associated model except invitation
            $user->swapNotifications($this);
            $user->swapProjects($this);
            //swap ...
            $user->delete();
        } else {
            $this->setRawAttributes(array_except($user->attributes, 'id'));
            $user->delete();
            $this->save();
        }

        return $this;
    }


    protected function swapNotifications(self $user): void
    {
        $this->notifications()->update(['notifiable_id' => $user->id]);
    }


    protected function swapProjects(self $user): void
    {
        $this->projects->pluck('id')->each(function ($item) use ($user) {
            $this->projects()->updateExistingPivot($item, ['user_id' => $user->id]);
        });
    }


    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership', 'to_be_deleted')
                    ->withTimestamps();
    }


    public function unassociatedProjects()
    {
        $associatedIds = $this->projects->pluck('id');

        return resolve(Project::class)->select('name', 'id')->whereNotIn('id', $associatedIds);
    }

}
