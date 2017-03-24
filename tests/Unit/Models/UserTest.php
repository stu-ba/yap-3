<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Yap\Exceptions\UserBannedException;
use Yap\Exceptions\UserNotConfirmedException;
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

    public function testUserIsMadeAnAdmin()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->makeAdmin();
        $this->assertTrue($user->is_admin);

        /** @var User $user */
        $user = factory(User::class, 'empty')->create();
        $user->makeAdmin();
        $this->assertTrue($user->is_admin);
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

    public function testUpdatingUserDoesNotChangeGithubId() {
        $user = factory(User::class)->create();
        $githubIdOriginal = $user->github_id;
        $githubId = rand(10, 30);
        $user->update(['name' => 'Joe', 'github_id' => $githubId]);

        $this->assertNotEquals($githubId, $user->github_id);
        $this->assertEquals($githubIdOriginal, $user->github_id);
    }
}
