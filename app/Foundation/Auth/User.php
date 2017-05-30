<?php

namespace Yap\Foundation\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

abstract class User extends Model implements AuthenticatableContract, AuthorizableContract
{

    use Authenticatable, Authorizable;
}
