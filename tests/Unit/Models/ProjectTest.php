<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Yap\Models\Invitation;
use Yap\Models\Project;

class ProjectTest extends TestCase
{

    use DatabaseMigrations;


    public function testLeaderCanBeAssignedToProject()
    {
        $this->withoutEvents();
        $user_id = factory(Invitation::class)->create()->user_id;
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addLeader($user_id);
        $project->fresh('leaders');

        $this->assertEquals(1, count($project->leaders));
    }

    public function testLeadersCanBeAssignedToProject()
    {
        $this->withoutEvents();
        $invitations = factory(Invitation::class, 2)->create();
        foreach ($invitations as $invitation) {
            $user_ids[] = $invitation->user_id;
        }
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addLeaders($user_ids);
        $project->fresh('leaders');

        $this->assertEquals(2, count($project->leaders));
    }

    public function testParticipantCanBeAssignedToProject()
    {
        $this->withoutEvents();
        $user_id = factory(Invitation::class)->create()->user_id;
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addParticipant($user_id);
        $project->fresh('participants');

        $this->assertEquals(1, count($project->participants));
    }

    public function testParticipantsCanBeAssignedToProject()
    {
        $this->withoutEvents();
        $invitations = factory(Invitation::class, 2)->create();
        foreach ($invitations as $invitation) {
            $user_ids[] = $invitation->user_id;
        }
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addParticipants($user_ids);
        $project->fresh('participants');

        $this->assertEquals(2, count($project->participants));
    }
}
