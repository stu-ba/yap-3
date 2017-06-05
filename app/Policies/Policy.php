<?php

namespace Yap\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

abstract class Policy
{

    use HandlesAuthorization;


    public function before($user, $ability)
    {
        return ($user->is_admin) ? true : null;
    }
}
