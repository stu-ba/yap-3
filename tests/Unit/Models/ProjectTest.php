<?php

namespace Tests\Unit\Models;

use Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Yap\Models\Invitation;
use Yap\Models\Project;

class ProjectTest extends TestCase
{

    use DatabaseMigrations;


    public function testLeaderCanBeAssignedToProject()
    {
        $user_id = factory(Invitation::class)->create()->user_id;

        Event::fake();
        $this->expectsModelEvents(Project::class, 'created');
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addLeader($user_id);
        $project->fresh('leaders');

        $this->assertEquals(1, count($project->leaders));
    }


    public function testLeadersCanBeAssignedToProject()
    {
        $user_ids = factory(Invitation::class, 2)->create()->pluck('user_id')->all();

        Event::fake();
        $this->expectsModelEvents(Project::class, 'created');
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addLeaders($user_ids);
        $project->fresh('leaders');

        $this->assertEquals(2, count($project->leaders));
    }


    public function testParticipantCanBeAssignedToProject()
    {
        $user_id = factory(Invitation::class)->create()->user_id;

        Event::fake();
        $this->expectsModelEvents(Project::class, 'created');
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addParticipant($user_id);
        $project->fresh('participants');

        $this->assertEquals(1, count($project->participants));
    }


    public function testParticipantsCanBeAssignedToProject()
    {
        $user_ids = factory(Invitation::class, 2)->create()->pluck('user_id')->all();

        Event::fake();
        $this->expectsModelEvents(Project::class, 'created');
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->addParticipants($user_ids);
        $project->fresh('participants');

        $this->assertEquals(2, count($project->participants));
    }
}
