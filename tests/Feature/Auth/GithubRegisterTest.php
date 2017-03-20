<?php

namespace Tests\Feature\Auth;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Yap\Models\Invitation;

class GithubRegisterTest extends TestCase
{

    use DatabaseMigrations, GithubMock;


    public function testUserIsShown404IfTokenDoesNotExists()
    {
        $register = route('register', ['token' => 'abc']);
        $this->get($register)->assertResponseStatus(404);
    }


    public function testUserCanNotUseDepletedTokenToRegister()
    {
        $invitation = factory(Invitation::class, 'empty')->states('depleted')->create();
        $register = route('register', ['token' => $invitation->token]);

        $this->get($register)->assertRedirectedToRoute('login');
    }


    public function testUserIsRedirectedToGithubWithEncryptedToken()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $register = route('register', ['token' => $invitation->token]);
        $encryptedToken = encrypt($invitation->token);
        $this->mockGithubRedirect($encryptedToken);

        $response = $this->get($register)->response;
        $this->assertEquals($this->buildGithubLoginUrl($encryptedToken), $response->getTargetUrl());
    }


    public function testUserIsRegisteredAndLoggedIn()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $faker = Factory::create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id'       => rand(1, 10),
            'token'    => $githubToken,
            'email'    => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name'     => $faker->firstName.' '.$faker->lastName,
            'avatar'   => $faker->imageUrl(),
            'user'     => ['bio' => 'abc']
        ]);

        $this->get(route('register.callback', ['token' => encrypt($invitation->token)]));

        $invitation = $invitation->fresh();

        $this->assertTrue($invitation->is_depleted);
        $this->assertTrue($invitation->user->is_confirmed);
        $this->seeIsAuthenticatedAs($invitation->user);
        $this->assertResponseStatus(302);
        $this->seeCookie('github_token', $githubToken);
    }


    public function testRegisterCallbackFailsIfTokenIsUndecryptable()
    {
        $this->get(route('register.callback', ['token' => str_random()]));
        $this->assertResponseStatus(400);
    }

}
