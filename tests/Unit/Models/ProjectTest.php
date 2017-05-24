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


    public function testMembersAreAdded()
    {
        $leaderIds = factory(Invitation::class, 2)->create()->pluck('user_id')->all();
        $participantIds = factory(Invitation::class, 4)->create()->pluck('user_id')->all();

        Event::fake();
        $this->expectsModelEvents(Project::class, 'created');
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project->syncMembers($leaderIds, $participantIds);
        $project->fresh('leaders', 'participants', 'members');

        $this->assertEquals(2, count($project->leaders));
        $this->assertEquals(4, count($project->participants));
        $this->assertEquals(6, count($project->members));
    }

    public function testMemberSyncSetsToBeDeletedFlag()
    {
        systemAccount();
        $leaderIds = factory(Invitation::class, 2)->create()->pluck('user_id')->all();
        $participantIds = factory(Invitation::class, 4)->create()->pluck('user_id')->all();

        Event::fake();
        $this->expectsModelEvents(Project::class, 'created');
        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->syncMembers($leaderIds, $participantIds);

        array_pop($leaderIds);
        array_pop($participantIds);

        $project->syncMembers($leaderIds, $participantIds);
        $project = $project->fresh('leaders', 'participants', 'members');

        $this->assertEquals(1, count($project->leaders->where('pivot.to_be_deleted', '=', false)));
        $this->assertEquals(3, count($project->participants->where('pivot.to_be_deleted', '=', false)));
        $this->assertEquals(4, count($project->members->where('pivot.to_be_deleted', '=', false)));
    }
}
