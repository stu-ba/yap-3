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
        return $current->is_admin && ! $user->is(systemAccount());
    }


    public function assignProjects(User $current)
    {
        return $current->is_admin || $current->isLeader();
    }


    public function unassignProjects(User $current)
    {
        return $current->is_admin || $current->isLeader();
    }


    public function filter(User $current)
    {
        return $current->is_admin;
    }


    public function seeEmail(User $current)
    {
        return $current->is_admin;
    }
}
