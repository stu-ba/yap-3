<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use InvalidArgumentException;
use Tests\Feature\Auth\Mocks\GithubMock;
use Tests\TestCase;
use Yap\Foundation\Auth\UserRegistrar;
use Yap\Models\Invitation;
use Yap\Models\User;

class UserRegistrarTest extends TestCase
{

    use DatabaseMigrations, GithubMock;

    /** @var UserRegistrar $registrar */
    private $registrar;


    function setUp()
    {
        parent::setUp();
        $this->registrar = resolve(UserRegistrar::class);
    }


    public function testRegisterByInvitation()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();
        /** @var User $user */
        $user = $this->registrar->register($invitation, $githubUser)->confirm();

        $this->assertTrue($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }

    public function testRegisterByInvalidInvitation()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states('depleted')->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();
        /** @var User $user */
        $user = $this->registrar->register($invitation, $githubUser);

        $this->assertFalse($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }

    public function testRegisterByGithubUser()
    {
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();

        /** @var User $user */
        $user = $this->registrar->register($githubUser);
        $user = $user->fresh();
        $this->assertFalse($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }

    public function testRegisterByGithubUserGivenValidInvitationExists()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser(['email' => $invitation->email]);

        /** @var User $user */
        $user = $this->registrar->register($githubUser);
        $user = $user->fresh();
        $invitation = $invitation->fresh();

        $this->assertTrue($invitation->is_depleted);
        $this->assertTrue($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }

    public function testRegisterByGithubUserGivenInvalidInvitationExists()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states('depleted')->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser(['email' => $invitation->email]);

        /** @var User $user */
        $user = $this->registrar->register($githubUser);
        $user = $user->fresh();
        $invitation = $invitation->fresh();

        $this->assertTrue($invitation->is_depleted);
        $this->assertFalse($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }

    public function testRegistrarArgumentOrderDoesNotMatter() {
        $invitation = factory(Invitation::class, 'empty')->make();
        $githubUser = $this->mockSocialiteUser($this->dummyGithubUserData());

        $thrown = false;

        try {
            $this->registrar->register($invitation, $githubUser);
        } catch (InvalidArgumentException $exception) {
            $thrown = true;
        } finally {
            $this->assertFalse($thrown);
        }

        $invitation = factory(Invitation::class, 'empty')->make();
        $githubUser = $this->mockSocialiteUser($this->dummyGithubUserData());
        $thrown = false;

        try {
            $this->registrar->register($githubUser, $invitation);
        } catch (InvalidArgumentException $exception) {
            $thrown = true;
        } finally {
            $this->assertFalse($thrown);
        }


    }


    public function testRegistrarThrowsExceptionBecauseNoArguments()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad number of arguments.');
        $this->registrar->register();
    }


    public function testRegistrarThrowsExceptionBecauseTooManyArguments()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad number of arguments.');
        $this->registrar->register(null, null, null);
    }


    public function testRegistrarThrowsExceptionBecauseArgumentsAreNotRecognized()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unrecognized type of argument.');
        $this->registrar->register(null, null);
    }
}
