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
        $user = factory(User::class)->states('confirmed')->create();
        $this->assertTrue($user->logginable());
    }


    public function testBannedExceptionIsThrownUponLoginable()
    {
        $this->expectException(UserBannedException::class);
        /** @var User $user */
        $user = factory(User::class)->states('banned')->create();
        $user->logginable();
    }


    public function testNotConfirmedExceptionIsThrownUponLoginable()
    {
        $this->expectException(UserNotConfirmedException::class);
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->logginable();
    }
}
