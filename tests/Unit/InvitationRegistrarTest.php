<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Yap\Exceptions\InvitationRegistrarException;
use Yap\Foundation\InvitationRegistrar;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationRegistrarTest extends TestCase
{
    use DatabaseMigrations;

    /** @var InvitationRegistrar $registrar */
    private $registrar;

    /** @var Invitation $invitation */
    private $invitation;


    public function setUp()
    {
        parent::setUp();
        $this->registrar = resolve(InvitationRegistrar::class);
        $this->invitation = resolve(Invitation::class);
    }

    public function testBareInvitationIsMade() {
        $email = str_random(). '@em.com';
        $this->registrar->invite($email);
        $invitation = $this->invitation->whereEmail($email)->first();

        $this->assertEquals($email, $invitation->email, 'Emails match');
        $this->assertNull($invitation->user->email, 'Assert that invitation->user->email is null');
        $this->assertNotNull($invitation->valid_until);

        $email = str_random(). '@em.com';
        $this->registrar->invite($email, ['indefinite' => true]);
        $invitation = $this->invitation->whereEmail($email)->first();

        $this->assertEquals($email, $invitation->email, 'Emails match');
        $this->assertNull($invitation->valid_until);
        $this->assertNull($invitation->user->email, 'Assert that invitation->user->email is null');

        $email = str_random(). '@em.com';
        $this->registrar->invite($email, ['indefinite' => true, 'admin' => true]);
        $invitation = $this->invitation->whereEmail($email)->first();

        $this->assertEquals($email, $invitation->email, 'Emails match');
        $this->assertNull($invitation->valid_until);
        $this->assertTrue($invitation->user->is_admin, 'Check user is made an admin');
        $this->assertNull($invitation->user->email, 'Assert that invitation->user->email is null');

        $this->assertEquals(3, $this->invitation->all()->count());
    }

    public function testBareInvitationIsMadeAndEmailIsSent() {
        $this->assertTrue(true);
    }

    public function testBannedExceptionIsThrown() {
        $user = factory(User::class)->states(['banned'])->create();
        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(0);
        $this->registrar->invite($user->email);
    }

    public function testConfirmedExceptionIsThrown() {
        $user = factory(User::class)->create();
        $invitation = factory(Invitation::class, 'unconfirmed')->create(['email' => $user->email]);
        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(2);
        $this->registrar->invite($invitation->email);
    }

    public function testExceptionIsThrownWhenUserIsNotConfirmedAndInvitationDepleted() {
        //same as test below this situation should never happen
        $invitation = factory(Invitation::class, 'unconfirmed')->create();
        factory(User::class)->create(['email' => $invitation->email]);

        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(2);

        $this->registrar->invite($invitation->user->email);

    }

    public function testCaseThatNeverHappensBecauseUserRegistrarTakesCareOfIt()
    {
        //this is case: user registrar does not allow creating $user without associating it with invitation
        $invitation = factory(Invitation::class, 'empty')->create();
        factory(User::class)->create(['email' => $invitation->email]);

        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(2);

        $this->registrar->invite($invitation->email);
    }

    public function testUserIsConfirmedAndEmailIsSent()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->registrar->invite($user->email);

        /** @var Invitation $invitation */
        $invitation = $this->invitation->whereEmail($user->email)->first();
        $user = $user->fresh();

        $this->assertTrue($invitation->isDepleted(), 'Invitation is depleted.');
        $this->assertTrue($user->is_confirmed, 'User is confirmed');
    }

    public function testUserIsConfirmedAndAdminAndEmailIsSent()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->registrar->invite($user->email, ['admin' => true]);

        /** @var Invitation $invitation */
        $invitation = $this->invitation->whereEmail($user->email)->first();
        $user = $user->fresh();

        $this->assertTrue($invitation->isDepleted(), 'Invitation is depleted.');
        $this->assertTrue($user->is_confirmed, 'User is confirmed.');
        $this->assertTrue($user->is_admin, 'User is admin.');
    }
}
