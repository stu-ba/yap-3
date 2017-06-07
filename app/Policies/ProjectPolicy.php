<?php

namespace Yap\Policies;

use Yap\Models\Project;
use Yap\Models\User;

class ProjectPolicy extends Policy
{

    /**
     * Determine whether the user can update the project.
     *
     * @param \Yap\Models\User     $current
     * @param  \Yap\Models\Project $project
     *
     * @return mixed
     */
    public function update(User $current, Project $project)
    {
        return $current->isLeaderTo($project);
    }


    public function edit(User $current, Project $project)
    {
        //TODO: forbid to update archive at date
        return $current->isLeaderTo($project);
    }


    public function removeMember(User $current, Project $project)
    {
        return $current->isLeaderTo($project);
    }


    public function addMember(User $current, Project $project)
    {
        return $current->isLeaderTo($project);
    }
}
