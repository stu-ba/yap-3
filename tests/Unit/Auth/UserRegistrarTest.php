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


    public function setUp()
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
        $user = $this->registrar->register($invitation, $githubUser)->fresh();

        $this->assertTrue($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }


    public function testRegisterByInvitationWithSameEmails()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser(['email' => $invitation->email]);
        /** @var User $user */
        $user = $this->registrar->register($invitation, $githubUser)->fresh();

        $this->assertTrue($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }


    public function testRegisterByDepletedInvitation()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states(['depleted'])->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();
        /** @var User $user */
        $user = $this->registrar->register($invitation, $githubUser)->fresh();

        $this->assertFalse($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }


    public function testRegisterByDepletedInvitationWithSameEmails()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states(['depleted'])->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser(['email' => $invitation->email]);
        /** @var User $user */
        $user = $this->registrar->register($invitation, $githubUser)->fresh();

        $this->assertFalse($user->is_confirmed);
        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }


    public function testRegisterByGithubUser()
    {
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser();

        /** @var User $user */
        $user = $this->registrar->register($githubUser)->fresh();
        $this->assertFalse($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }


    public function testRegisterByGithubUserGivenValidInvitationExistsWithSameEmails()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser(['email' => $invitation->email]);

        /** @var User $user */
        $user = $this->registrar->register($githubUser)->fresh();
        $invitation = $invitation->fresh();

        $this->assertTrue($invitation->is_depleted);
        $this->assertTrue($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }


    public function testRegisterByGithubUserGivenInvalidInvitationExistsWithSameEmails()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states(['depleted'])->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser(['email' => $invitation->email]);

        /** @var User $user */
        $user = $this->registrar->register($githubUser)->fresh();

        $invitation = $invitation->fresh();

        $this->assertTrue($invitation->is_depleted);
        $this->assertFalse($user->is_confirmed);

        foreach ($userData as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }


    public function testRegisterByGithubUserGivenValidInvitationExists()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser([
            'email' => $user->email,
            'id'    => $user->github_id,
        ]);

        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();

        /** @var User $user */
        $user = $this->registrar->register($invitation, $githubUser)->fresh();
        $invitation = $invitation->fresh();

        $this->assertTrue($invitation->is_depleted);
        $this->assertEquals($user->id, $invitation->user_id);
        $this->assertTrue($user->is_confirmed);
    }


    public function testRegisterByGithubUserGivenInvalidInvitationExists()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser([
            'email' => $user->email,
            'id'    => $user->github_id,
        ]);

        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states(['depleted'])->create();

        /** @var User $user */
        $user = $this->registrar->register($invitation, $githubUser)->fresh();
        $this->assertNull($invitation->user->email);
        $this->assertFalse($user->is_confirmed);
    }


    public function testRegisterByInvitationDifferentThanMine()
    {
        /** @var Invitation $invitation2 */
        $invitation2 = factory(Invitation::class)->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();

        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser([
            'id'    => $invitation2->user->github_id,
            'email' => $invitation2->user->email,
        ]);

        $this->registrar->register($invitation, $githubUser);

        $invitation = $invitation->fresh();
        $invitation2 = $invitation2->fresh();

        $this->assertTrue($invitation2->isDepleted(), 'Invitation 2 is not depleted.');
        $this->assertFalse($invitation->isDepleted(), 'Invitation is depleted.');

        $this->assertTrue($invitation2->user->is_confirmed);

    }


    public function testRegisterNotIfAlreadyRegistered()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class)->create();
        /** @var Invitation $invitationEmpty */
        $invitationEmpty = factory(Invitation::class, 'empty')->create();

        list($githubUser, $userData) = $this->generateDummyUserDataAndGithubUser([
            'email' => $invitation->user->email,
            'id'    => $invitation->user->github_id,
        ]);

        $expected = $invitation->user_id;
        $expectedEmpty = $invitationEmpty->user_id;

        $this->registrar->register($invitationEmpty, $githubUser);
        $this->assertEquals($expected, $invitation->fresh()->user_id);
        $this->assertEquals($expectedEmpty, $invitationEmpty->fresh()->user_id);

    }


    public function testRegistrarArgumentOrderDoesNotMatter()
    {
        $invitation = factory(Invitation::class, 'empty')->make();
        $githubUser = $this->mockSocialiteUser($this->dummyGithubUserData());

        $thrown = false;

        try {
            $this->registrar->register($invitation, $githubUser);
        } catch (InvalidArgumentException $exception) {
            $thrown = true;
        }
        finally {
            $this->assertFalse($thrown);
        }

        $invitation = factory(Invitation::class, 'empty')->make();
        $githubUser = $this->mockSocialiteUser($this->dummyGithubUserData());
        $thrown = false;

        try {
            $this->registrar->register($githubUser, $invitation);
        } catch (InvalidArgumentException $exception) {
            $thrown = true;
        }
        finally {
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
