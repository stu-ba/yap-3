<?php

namespace Tests\Unit\Models;

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

    public function testTokenIsNotValidBecauseCreatorIsBanned()
    {
        /** @var User $bannedUser */
        $bannedUser = factory(User::class)->states(['banned'])->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['created_by' => $bannedUser->id]);
        $this->assertTrue($invitation->isDepleted());
    }

    public function testTokenIsDepleted()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->states(['depleted'])->create();
        $this->assertTrue($invitation->isDepleted());

        $invitation = factory(Invitation::class)->create();
        $this->assertTrue($invitation->isDepleted());
    }

    public function testTokenIsNotValidBecauseItExpired()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $this->assertTrue($invitation->isDepleted());
    }

    public function testTokenIsValid()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->assertFalse($invitation->isDepleted());
    }

    public function testTokenIsValidForever()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => null]);
        $this->assertFalse($invitation->isDepleted());
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

    public function testInvitationDepletion()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $invitation->deplete();

        $this->assertTrue($invitation->is_depleted);
    }

    public function testDetermineValidUntil()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->assertNotNull($invitation->valid_until);

        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => 0]);
        $this->assertNull($invitation->valid_until);

        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->addDay()]);
        $this->assertNotNull($invitation->valid_until);
    }
}
