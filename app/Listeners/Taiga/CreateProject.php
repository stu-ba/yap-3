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
    public function handle(ProjectCreated $event)
    {
        $project = $event->project;
        $taigaProject = $this->taiga->createProject($project);

        $project->update(['taiga_id' => $taigaProject->id]);
    }
}
