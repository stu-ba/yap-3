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

    public function testTokenIsNotValidBecauseInviterIsBanned()
    {
        /** @var User $bannedUser */
        $bannedUser = factory(User::class)->states(['banned'])->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['invited_by' => $bannedUser->id]);
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

    public function testIsProlonged() {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $invitation->prolong();
        $this->assertTrue(Carbon::now()->lt($invitation->valid_until));
        $invitation->prolong(Carbon::now()->subDays(20));
        $this->assertTrue(Carbon::now()->lt($invitation->valid_until));
    }

    public function testInviterIsUpdated() {
        /** @var User $user */
        $user = factory(User::class)->states(['admin'])->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()]);
        $this->actingAs($user);
        $invitation->updateInviter($user);
        $this->assertEquals($user->id, $invitation->inviter->id);
    }

    public function testInviterIsNotUpdated() {
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()]);
        $this->actingAs($user);
        $invitation->updateInviter($user);
        $this->assertNotEquals($user->id, $invitation->inviter->id);
    }

    public function testMakeIndefinite() {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $invitation->makeIndefinite();
        $this->assertNull($invitation->valid_until);
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

    public function testInvitationHasInviter()
    {
        /** @var Invitation $invitation */
        $invitation = factory(Invitation::class, 'empty')->create();
        $this->assertInstanceOf(User::class, $invitation->inviter);
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

    public function testInvitationUserIsSwapped() {
        // swap filled user with empty user
        $invitation = factory(Invitation::class, 'empty')->create();
        $user = factory(User::class)->create();

        $invitation->swapUser($user);
        $this->assertEquals($invitation->user_id, $user->id);

        // swap empty user with already confirmed user (swap only relations)
        $invitation = factory(Invitation::class)->create();
        $invitation2 = factory(Invitation::class, 'empty')->create();

        $invitation2->swapUser($invitation->user);
        $this->assertEquals($invitation->user_id, $invitation2->user_id);

    }
}
