<?php

namespace Tests\Feature\Auth;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Yap\Models\Invitation;
use Yap\Models\User;

class GithubLogin extends TestCase
{

    use DatabaseMigrations, GithubMock;


    public function testUserSeeLoginPage()
    {
        $this->visitRoute('login')->seeText('Login to Yap 3.0');
    }

    public function testUserIsLoggedIn()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class)->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id'       => $invitation->user->github_id,
            'token'    => $githubToken,
            'email'    => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name'     => $faker->firstName.' '.$faker->lastName,
            'avatar'   => $faker->imageUrl(),
            'user'     => ['bio' => 'abc']
        ]);

        $this->get(route('login.callback'));

        $this->seeIsAuthenticatedAs($invitation->user);
        $this->seeCookie('github_token', $githubToken);
        $this->assertResponseStatus(302);
    }

    public function testUserIsNotLoggedInIfNotConfirmed()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class)->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id'       => $invitation->user->github_id,
            'token'    => $githubToken,
            'email'    => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name'     => $faker->firstName.' '.$faker->lastName,
            'avatar'   => $faker->imageUrl(),
            'user'     => ['bio' => 'abc']
        ]);

        $this->get(route('login.callback'));

        $this->seeIsAuthenticatedAs($invitation->user);
        $this->seeCookie('github_token', $githubToken);
        $this->assertResponseStatus(302);
    }

}
