<?php

namespace Yap\Listeners\Taiga;

use Yap\Events\ProjectCreated;

class CreateProject extends Taiga
{

    /**
     * Handle the event.
     *
     * @param  ProjectCreated $event
     *
     * @return void
     */
    protected function handle(ProjectCreated $event)
    {
        /** @var \Yap\Models\Project $project */
        $project      = $event->project;

        if (!is_null($project->taiga_id)) {
            return;
        }

        $taigaProject = $this->taiga->createProject($project);
        $project->update(['taiga_id' => $taigaProject->id]);
    }
}
