<?php

namespace Tests\Unit;

use Carbon\Carbon;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIsTokenValid()
    {
        /** @var Invitation $invitation */
        /** @var Invitation $factoryInv */
        $invitation = resolve(Invitation::class);

        //Test non-existing token
        $this->assertFalse($invitation->isTokenValid('abc'));

        /** @var User $bannedUser */
        $bannedUser = factory(User::class)->states('banned')->create();
        $factoryInv = factory(Invitation::class, 'empty')->create(['created_by' => $bannedUser->id]);
        $this->assertFalse($invitation->isTokenValid($factoryInv->token));

        //Test depleted, this state never exists in app
        $factoryInv = factory(Invitation::class, 'empty')->states('depleted')->create();
        $this->assertFalse($invitation->isTokenValid($factoryInv->token));

        //Test depleted
        $factoryInv = factory(Invitation::class)->create();
        $this->assertFalse($invitation->isTokenValid($factoryInv->token));

        //Test valid until
        $factoryInv = factory(Invitation::class, 'empty')->create(['valid_until' => Carbon::now()->subDay()]);
        $this->assertFalse($invitation->isTokenValid($factoryInv->token));

        //Test token is valid
        $factoryInv = factory(Invitation::class, 'empty')->create();
        $this->assertTrue($invitation->isTokenValid($factoryInv->token));
    }
}
