<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Testing\Fakes\MailFake;
use Tests\TestCase;
use Yap\Exceptions\InvitationRegistrarException;
use Yap\Foundation\InvitationRegistrar;
use Yap\Mail\InvitationProlonged;
use Yap\Mail\InvitationUrged;
use Yap\Mail\UserAccessGranted;
use Yap\Mail\UserInvited;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationRegistrarTest extends TestCase
{

    use DatabaseMigrations;

    /** @var MailFakeInvitationRegistrar $registrar */
    private $registrar;

    /** @var Invitation $invitation */
    private $invitation;


    public function setUp()
    {
        parent::setUp();
        $this->registrar = resolve(MailFakeInvitationRegistrar::class);
        $this->invitation = resolve(Invitation::class);
    }


    public function testBareInvitationIsMade()
    {
        $email = str_random().'@em.com';
        $this->registrar->invite($email);
        $invitation = $this->invitation->whereEmail($email)->first();

        $this->assertEquals($email, $invitation->email, 'Emails match');
        $this->assertNull($invitation->user->email, 'Assert that invitation->user->email is null');
        $this->assertNotNull($invitation->valid_until);

        $email = str_random().'@em.com';
        $this->registrar->invite($email, ['indefinite' => true]);
        $invitation = $this->invitation->whereEmail($email)->first();

        $this->assertEquals($email, $invitation->email, 'Emails match');
        $this->assertNull($invitation->valid_until);
        $this->assertNull($invitation->user->email, 'Assert that invitation->user->email is null');

        $email = str_random().'@em.com';
        $this->registrar->invite($email, ['indefinite' => true, 'admin' => true]);
        $invitation = $this->invitation->whereEmail($email)->first();

        $this->assertEquals($email, $invitation->email, 'Emails match');
        $this->assertNull($invitation->valid_until);
        $this->assertTrue($invitation->user->is_admin, 'Check user is made an admin');
        $this->assertNull($invitation->user->email, 'Assert that invitation->user->email is null');

        $this->assertEquals(3, $this->invitation->all()->count());
    }


    public function testBareInvitationEmailIsSent()
    {
        $email = str_random(32).'@email.com';
        $this->registrar->invite($email);
        $this->assertEquals($email, $this->invitation->first()->email);
        $this->registrar->mailer->assertSent(UserInvited::class, function ($mail) use ($email) {
            return $mail->invitation->email === $email;
        });
    }


    public function testBareInvitationEmailIsQueued()
    {
        $registrar = resolve(InvitationRegistrar::class);
        $email = str_random(32).'@email.com';

        Queue::fake();
        $this->expectsJobs(SendQueuedMailable::class);

        $registrar->invite($email);
        $this->assertEquals($email, $this->invitation->first()->email);
    }


    public function testInvitationUrgedEmailIsSent()
    {
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => 0]);
        $this->registrar->invite($invitation->email);
        $this->assertEquals($invitation->email, $this->invitation->first()->email);
        $this->registrar->mailer->assertSent(InvitationUrged::class, function ($mail) use ($invitation) {
            return $mail->invitation->email === $invitation->email;
        });
    }


    public function testInvitationUrgedEmailQueued()
    {
        $registrar = resolve(InvitationRegistrar::class);
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => 0]);

        Queue::fake();
        $this->expectsJobs(SendQueuedMailable::class);

        $registrar->invite($invitation->email);
        $this->assertEquals($invitation->email, $this->invitation->first()->email);
    }


    public function testInvitationProlongedEmailIsSent()
    {
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $this->registrar->invite($invitation->email);
        $this->assertEquals($invitation->email, $this->invitation->first()->email);
        $this->registrar->mailer->assertSent(InvitationProlonged::class, function ($mail) use ($invitation) {
            return $mail->invitation->email === $invitation->email;
        });
    }


    public function testInvitationProlongedEmailQueued()
    {
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $registrar = resolve(InvitationRegistrar::class);

        Queue::fake();
        $this->expectsJobs(SendQueuedMailable::class);

        $registrar->invite($invitation->email);
        $this->assertEquals($invitation->email, $this->invitation->first()->email);
    }


    public function testInvitationUrgeIfProlongedToIndefiniteEmailIsSent()
    {
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $this->registrar->invite($invitation->email, ['indefinite' => true]);
        $this->assertEquals($invitation->email, $this->invitation->first()->email);
        $this->registrar->mailer->assertSent(InvitationUrged::class, function ($mail) use ($invitation) {
            return $mail->invitation->email === $invitation->email;
        });
    }


    public function testNoEmailIsSentIfProlongedDuringValidUntilPeriod()
    {
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->registrar->invite($invitation->email);
        $this->assertEquals($invitation->email, $this->invitation->first()->email);
        $this->registrar->mailer->assertNilSent();
    }


    public function testEmailIsSentIfProlongedDuringValidUntilPeriodAndForceResendOptionIsPassed()
    {
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->registrar->invite($invitation->email, ['force_resend' => true]);
        $this->assertEquals($invitation->email, $this->invitation->first()->email);
        $this->registrar->mailer->assertSent(InvitationProlonged::class, function ($mail) use ($invitation) {
            return $mail->invitation->email === $invitation->email;
        });
    }


    public function testBannedExceptionIsThrown()
    {
        $user = factory(User::class)->states(['banned'])->create();
        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(0);
        $this->registrar->invite($user->email);
    }


    public function testConfirmedExceptionIsThrown()
    {
        $user = factory(User::class)->create();
        $invitation = factory(Invitation::class, 'unconfirmed')->create(['email' => $user->email]);
        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(2);
        $this->registrar->invite($invitation->email);
    }


    public function testExceptionIsThrownWhenUserIsNotConfirmedAndInvitationDepleted()
    {
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

        $this->registrar->mailer->assertSent(UserAccessGranted::class, function ($mail) use ($user) {
            return $mail->user->email === $user->email;
        });
    }


    public function testUserIsConfirmedAndIsAdmin()
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

        $this->registrar->mailer->assertSent(UserAccessGranted::class, function ($mail) use ($user) {
            return $mail->user->email === $user->email;
        });
    }
}

class MailFakeInvitationRegistrar extends InvitationRegistrar
{

    public $mailer;


    public function __construct(User $user, Invitation $invitation, Mailer $mailer)
    {
        parent::__construct($user, $invitation, $mailer);
        $this->mailer = new MailFake;
    }
}
