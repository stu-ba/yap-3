<?php

namespace Tests\Feature\Auth;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Yap\Models\Invitation;

class GithubLogin extends TestCase
{
    use DatabaseMigrations, GithubMock;

    public function testUserSeeLoginPage()
    {
        $this->visitRoute('login')->seeText('Login to Yap 3.0');
    }

    public function testCallbackRedirectsAfterStateException()
    {
        $this->visitRoute('login.callback')->seeRouteIs('login');
    }

    public function testUserIsLoggedIn()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class)->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $invitation->user->github_id,
            'token' => $githubToken,
            'email' => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));

        $this->seeIsAuthenticatedAs($invitation->user);
        $this->seeCookie('github_token', $githubToken);
        $this->assertResponseStatus(302);
    }

    public function testUserIsNotLoggedInIfNotConfirmed()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'unconfirmed')->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $invitation->user->github_id,
            'token' => $githubToken,
            'email' => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));

        $this->dontSeeIsAuthenticated();
        $this->assertResponseStatus(403);
    }

    public function testUserIsNotLoggedInIfBanned()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'banned')->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $invitation->user->github_id,
            'token' => $githubToken,
            'email' => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));

        $this->dontSeeIsAuthenticated();
        $this->assertResponseStatus(403);
    }


    public function testNewGithubUserIsLoggedInIfInvitationIsNotDepletedAndEmailMatches()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $faker->randomNumber(9, true),
            'token' => $githubToken,
            'email' => $invitation->email,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));
        $invitation = $invitation->fresh();

        $this->assertTrue($invitation->is_depleted);
        $this->assertTrue($invitation->user->is_confirmed);
        $this->seeIsAuthenticatedAs($invitation->user);
        $this->seeCookie('github_token', $githubToken);
        $this->assertResponseStatus(302);
    }

    public function testNewGithubUserIsNotLoggedIfInvitationIsDepletedAndEmailMatches()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states('depleted')->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $faker->randomNumber(9, true),
            'token' => $githubToken,
            'email' => $invitation->email,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));

        $this->dontSeeIsAuthenticated();
        $this->assertResponseStatus(403);
    }

    public function testNewGithubUserIsNotLoggedInIfInvitationIsNotDepletedAndEmailDoesNotMatch()
    {
        factory(Invitation::class, 'empty')->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $faker->randomNumber(9, true),
            'token' => $githubToken,
            'email' => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));

        $this->dontSeeIsAuthenticated();
        $this->assertResponseStatus(403);
    }

    public function testNewGithubUserIsNotLoggedInIfInvitationIsDepletedAndEmailDoesNotMatch()
    {
        factory(Invitation::class, 'empty')->states(['depleted'])->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $faker->randomNumber(9, true),
            'token' => $githubToken,
            'email' => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));

        $this->dontSeeIsAuthenticated();
        $this->assertResponseStatus(403);
    }

    public function testNewGithubUserIsNotLoggedIfNoInvitationIsFound()
    {
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id' => $faker->randomNumber(9, true),
            'token' => $githubToken,
            'email' => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name' => $faker->firstName.' '.$faker->lastName,
            'avatar' => $faker->imageUrl(),
            'user' => ['bio' => 'abc'],
        ]);

        $this->get(route('login.callback'));

        $this->dontSeeIsAuthenticated();
        $this->assertResponseStatus(403);
    }
}
