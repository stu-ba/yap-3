<?php

namespace Yap\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Yap\Models\Project;
use Yap\Models\User;

class ProjectPolicy
{

    use HandlesAuthorization;

    public function before($user, $ability)
    {
        return ($user->is_admin) ? true : null;
    }

    public function archive(User $user, Project $project) {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param \Yap\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }


    /**
     * Determine whether the user can update the project.
     *
     * @param \Yap\Models\User     $user
     * @param  \Yap\Models\Project $project
     *
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return $project->leaders->contains('username', $user->username);
    }

    public function removeMember(User $user, Project $project) {
        //TODO: finish me
    }


    /**
     * Determine whether the user can delete the project.
     *
     * @param \Yap\Models\User     $user
     * @param  \Yap\Models\Project $project
     *
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        //
    }
}
