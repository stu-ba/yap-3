<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Tests\Unit\Auth\Stubs\ControllerStub;
use Yap\Exceptions\UserBannedException;
use Yap\Exceptions\UserNotConfirmedException;
use Yap\Models\User;

class AuthenticableTest extends TestCase
{
    use DatabaseMigrations, GithubMock;

    /** @var ControllerStub $authenticable */
    private $authenticable;

    /**
     * Sets up the fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->authenticable = new ControllerStub();
        parent::setUp();
    }

    public function testUserIsLoggedIn()
    {
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();
        /** @var User $user */
        $user = factory(User::class)->states(['confirmed'])->create($userData);

        $this->authenticable->attempt($user);
        $this->seeIsAuthenticatedAs($user);
    }

    public function testBannedUserIsCanNotLogIn()
    {
        $this->expectException(UserBannedException::class);
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();
        /** @var User $user */
        $user = factory(User::class)->states(['banned'])->create($userData);

        $this->authenticable->attempt($user);
        $this->dontSeeIsAuthenticated();
    }

    public function testNotConfirmedUserIsCanNotLogIn()
    {
        $this->expectException(UserNotConfirmedException::class);
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();
        /** @var User $user */
        $user = factory(User::class)->create($userData);

        $this->authenticable->attempt($user);
        $this->dontSeeIsAuthenticated();
    }

    public function testResponseHasCookie()
    {
        $this->authenticable->setGithubTokenCookie(str_random());
        $this->response = $this->authenticable->response();

        $this->seeCookie('github_token');
        $this->seeStatusCode(302);
        $this->assertRedirectedTo('home');
    }
}
