<?php

namespace Yap\Listeners\Github;

use Yap\Events\RepositoryRequested;
use Yap\Events\TeamRequested;

class CreateTeam extends Github
{

    /**
     * Handle the event.
     *
     * @param  TeamRequested $event
     *
     * @return void
     */
    protected function handle(TeamRequested $event)
    {
        $project = $event->project;
        $team    = $this->github->createTeam($project->slugged,
            'Project '.$project->name.' participants with write permissions.');
        $project->update(['github_team_id' => $team['id']]);
        event(new RepositoryRequested($project));
    }
}
