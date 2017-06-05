<?php

namespace Yap\Policies;

use Yap\Models\User;

class UserPolicy extends Policy
{

    public function before($user, $ability)
    {
        return null;
    }


    public function manage(User $current, User $user)
    {
        return ! $current->is($user) && $current->is_admin;
    }


    public function assignProjects(User $current)
    {
        return $current->isLeader();
    }
}
