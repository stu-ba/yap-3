<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Yap\Events\UserDemoted;
use Yap\Events\UserPromoted;
use Yap\Exceptions\UserBannedException;
use Yap\Exceptions\UserNotConfirmedException;
use Yap\Models\Invitation;
use Yap\Models\Project;
use Yap\Models\User;

class UserTest extends TestCase
{

    use DatabaseMigrations, GithubMock;


    public function testUserIsLoginable()
    {
        /** @var User $user */
        $user = factory(User::class)->states(['confirmed'])->create();
        $this->assertTrue($user->logginable());
    }


    public function testUserIsPromoted()
    {
        Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();
        $this->expectsEvents(UserPromoted::class);
        $user->promote();

        $this->assertTrue($user->is_admin, 'Test that user is an administrator.');
    }


    public function testUserIsPromotedWithoutEvent()
    {
        $user = factory(User::class, 'empty')->create();
        $this->doesntExpectEvents(UserPromoted::class);

        $user->promote();

        $this->assertTrue($user->is_admin, 'Test that user is basic user.');
    }


    public function testUserIsDemoted()
    {
        Event::fake();

        /** @var User $user */
        $user = factory(User::class)->states(['admin'])->create();
        $this->expectsEvents(UserDemoted::class);

        $user->demote();

        $this->assertFalse($user->is_admin, 'Test that user is basic user.');
    }


    public function testUserIsDemotedWithoutEvent()
    {
        $user = factory(User::class, 'empty')->states(['admin'])->create();
        $this->doesntExpectEvents(UserDemoted::class);

        $user->demote();

        $this->assertFalse($user->is_admin, 'Test that user is basic user.');
    }


    public function testBannedExceptionIsThrownUponLoginable()
    {
        $this->expectException(UserBannedException::class);
        /** @var User $user */
        $user = factory(User::class)->states(['banned'])->create();
        $user->logginable();
    }


    public function testNotConfirmedExceptionIsThrownUponLoginable()
    {
        $this->expectException(UserNotConfirmedException::class);
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->logginable();
    }


    public function testColleaguesIds()
    {
        factory(Invitation::class, 10)->create();

        Event::fake();
        /**@var Project $project */
        $projects = factory(Project::class, 2)->create();
        $user     = resolve(User::class);
        $userIds  = $user->all()->whereNotIn('id', [systemAccount()->id])->pluck('id');

        $projects->first()->addParticipants($userIds->nth(2)->all());
        $projects->last()->addParticipants($userIds->nth(3)->all());

        $this->assertEquals([3, 5, 7, 9, 4, 10], $user->find(1)->colleaguesIds());
    }


    public function testUpdatingUserDoesNotChangeGithubId()
    {
        $user             = factory(User::class)->create();
        $githubIdOriginal = $user->github_id;
        $githubId         = rand(10, 30);
        $user->update(['name' => 'Joe', 'github_id' => $githubId]);

        $this->assertEquals('Joe', $user->name);
        $this->assertNotEquals($githubId, $user->github_id);
        $this->assertEquals($githubIdOriginal, $user->github_id);
    }


    public function testSwappingConfirmedUser()
    {
        /** @var User $userConfirmed */
        $userConfirmed = factory(User::class)->states(['confirmed'])->create();
        /** @var User $userEmpty */
        $userEmpty = factory(User::class, 'empty')->create();

        $this->withoutModelEvents();
        /** @var \Illuminate\Database\Eloquent\Collection $projects */
        $projects = factory(Project::class, 3)->create();
        $projects->each->addParticipant($userEmpty->id);

        $userEmpty->notify(new \Yap\Notifications\PromotedNotification);
        $userEmpty->notify(new \Yap\Notifications\DemotedNotification);

        $userConfirmed->swapWith($userEmpty);

        $this->assertEquals(2, $userConfirmed->notifications->count());
        $this->assertEquals(3, $userConfirmed->projects->count());
        $this->assertNull($userEmpty->fresh());
    }


    public function testSwappingEmptyUser()
    {
        $userEmpty = factory(User::class, 'empty')->create();
        $user      = factory(User::class)->create();

        $userEmpty->swapWith($user);

        $this->assertEquals($userEmpty->email, $user->email);
        $this->assertEquals($userEmpty->github_id, $user->github_id);
        $this->assertNotEquals($userEmpty->id, $user->id);

        $this->assertNull($user->fresh());
    }
}
