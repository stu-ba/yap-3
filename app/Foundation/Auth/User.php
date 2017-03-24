<?php

namespace Yap\Foundation\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * Yap\Foundation\Auth\User
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
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereBanReason($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereBio($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereGithubId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereIsBanned($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereIsConfirmed($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereTaigaId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Auth\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{

    use Authenticatable, Authorizable;
}
