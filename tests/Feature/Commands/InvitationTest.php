<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use InvalidArgumentException;
use Tests\ForceArrayMailerDriver;
use Tests\ForceSyncQueueDriver;
use Tests\TestCase;
use Yap\Console\Kernel;
use Yap\Exceptions\InvitationRegistrarException;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationTest extends TestCase
{

    use DatabaseMigrations, ForceSyncQueueDriver, ForceArrayMailerDriver;

    /**
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     *
     * @var Invitation
     */
    protected $invitation;


    public function setUp()
    {
        parent::setUp();
        $this->kernel = resolve(Kernel::class);
        $this->invitation = resolve(Invitation::class);
    }


    public function testEmailIsValidated()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->kernel->call('yap:invitation', ['email' => str_random()]);
    }


    public function testBasicUsage()
    {
        $email = str_random().'@some-random-domain.com';
        $this->kernel->call('yap:invitation', ['email' => $email]);
        $this->assertEquals($email, $this->invitation->first()->email);
    }


    public function testUserBannedException()
    {
        $user = factory(User::class)->states(['banned'])->create();
        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(0);
        $this->kernel->call('yap:invitation', ['email' => $user->email]);
    }


    public function testUserConfirmedException()
    {
        $invitation = factory(Invitation::class)->create();
        $this->expectException(InvitationRegistrarException::class);
        $this->expectExceptionCode(1);
        $this->kernel->call('yap:invitation', ['email' => $invitation->email]);
    }
}
