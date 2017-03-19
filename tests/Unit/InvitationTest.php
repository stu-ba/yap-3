<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationTest extends TestCase
{

    use DatabaseMigrations;

    public function testInvitationThrowsExceptionIfTokenDoesNotExists()
    {
        $this->expectException(ModelNotFoundException::class);
        $invitation = resolve(Invitation::class);
        $invitation->whereToken('abc')->firstOrFail();
    }

    public function testTokenIsNotValidBecauseCreatorIsBanned() {
        /** @var User $bannedUser */
        $bannedUser = factory(User::class)->states('banned')->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['created_by' => $bannedUser->id]);
        $this->assertFalse($invitation->isTokenValid());
    }

    public function testTokenIsNotValidBecauseItsDepleted() {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states('depleted')->create();
        $this->assertFalse($invitation->isTokenValid());

        $invitation = factory(Invitation::class)->create();
        $this->assertFalse($invitation->isTokenValid());
    }

    public function testTokenIsNotValidBecauseItExpired()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $this->assertFalse($invitation->isTokenValid());
    }

    public function testTokenIsValid()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->assertTrue($invitation->isTokenValid());

    }


    public function testInvitationHasCreator()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->assertInstanceOf(User::class, $invitation->creator);
    }


    public function testInvitationHasUser()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->assertInstanceOf(User::class, $invitation->user);
    }

    public function testInvitationInvalidation()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $invitation->invalidate();

        $this->assertTrue($invitation->is_depleted);
    }
}
