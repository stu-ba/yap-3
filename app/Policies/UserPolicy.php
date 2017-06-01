<?php

namespace Yap\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Yap\Models\User;

class UserPolicy
{

    use HandlesAuthorization;


    public function ban(User $current, User $user)
    {
        return $this->isAdminExceptSelf($current, $user);
    }


    private function isAdminExceptSelf(User $current, User $user)
    {
        if ($current != $user && $current->is_admin) {
            return true;
        }

        return false;
    }


    public function unban(User $current, User $user)
    {
        return $this->isAdminExceptSelf($current, $user);
    }


    public function promote(User $current, User $user)
    {
        return $this->isAdminExceptSelf($current, $user);
    }


    public function demote(User $current, User $user)
    {
        return $this->isAdminExceptSelf($current, $user);
    }
}
