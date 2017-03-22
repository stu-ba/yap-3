<?php

namespace Tests\Feature\Auth;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Yap\Models\Invitation;
use Yap\Models\User;

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
            'id'       => $faker->randomNumber(9, true),
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
        $this->seeCookie('github_token', $githubToken);
        $this->assertResponseStatus(302);
    }

    //Hopefully this will NEVER happen
    public function testUserIsRegisteredAndNotLoggedInBecauseRegistrationTokenUsedByDifferentRegisteredUser()
    {
        $faker = Factory::create();

        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class)->states('!depleted')->create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id'       => $faker->randomNumber(9, true),
            'token'    => $githubToken,
            //'email'    => $invitation->user->email,
            'email'    => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name'     => $faker->firstName.' '.$faker->lastName,
            'avatar'   => $faker->imageUrl(),
            'user'     => ['bio' => 'abc']
        ]);

        $this->get(route('register.callback', ['token' => encrypt($invitation->token)]));

        $this->dontSeeIsAuthenticated();
        $this->assertResponseStatus(403);
    }

    public function testUserIsRegisteredAndLoggedInWhenInvitationIsNotDepletedAndEmailDoesNotMatchInvitedUser()
    {
        $faker = Factory::create();

        /** @var Invitation $invitation2 */
        $invitation2 = factory(Invitation::class)->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class)->states('!depleted')->create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id'       => $invitation2->user->github_id,
            'token'    => $githubToken,
            'email'    => $invitation2->user->email,
            'nickname' => $faker->userName,
            'name'     => $faker->firstName.' '.$faker->lastName,
            'avatar'   => $faker->imageUrl(),
            'user'     => ['bio' => 'abc']
        ]);

        $this->get(route('register.callback', ['token' => encrypt($invitation->token)]));

        $this->seeIsAuthenticatedAs($invitation2->fresh()->user);
        $this->seeCookie('github_token', $githubToken);
        $this->assertResponseStatus(302);
    }

    public function testUserIsRegisteredAndLoggedInGivenValidNonDepletedInvitationIsProvided()
    {
        $faker = Factory::create();
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();

        $githubToken = str_random(24);
        $this->mockSocialiteFacade([
            'id'       => $user->github_id,
            'token'    => $githubToken,
            'email'    => $user->email,
            'nickname' => $faker->userName,
            'name'     => $faker->firstName.' '.$faker->lastName,
            'avatar'   => $faker->imageUrl(),
            'user'     => ['bio' => 'abc']
        ]);

        $this->get(route('register.callback', ['token' => encrypt($invitation->token)]));
        $this->seeIsAuthenticatedAs($user->fresh());
        $this->assertResponseStatus(302);
    }


    public function testRegisterCallbackFailsIfTokenIsUndecryptable()
    {
        $this->get(route('register.callback', ['token' => str_random()]));
        $this->assertResponseStatus(400);
    }
}
