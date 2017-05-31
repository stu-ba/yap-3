<?php

namespace Yap\Listeners\Github;

use Yap\Events\RepositoryRequested;

class CreateRepository extends Github
{

    /**
     * Handle the event.
     *
     * @param  RepositoryRequested $event
     *
     * @return void
     */
    protected function handle(RepositoryRequested $event)
    {
        $project    = $event->project;
        $repository = $this->github->createRepository(str_slug($project->name), str_limit($project->description),
            $project->github_team_id);
        $project->update(['github_repository_id' => $repository['id']]);
    }
}
