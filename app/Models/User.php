<?php

namespace Yap\Models;

use Yap\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
 * @property string $avatar
 * @property bool $is_admin
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereBio($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereGithubId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\User whereIsAdmin($value)
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
        'avatar',
        'is_admin'
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
        'is_admin' => 'boolean',
    ];

    public function system() {
        return $this->whereTaigaId('0')->whereGithubId('0')->whereIsAdmin(true)->first();
    }
}
