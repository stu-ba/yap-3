<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\ForceArrayMailerDriver;
use Tests\ForceSyncQueueDriver;
use Tests\TestCase;
use Yap\Models\Invitation;

class IndexTest extends TestCase
{

    use DatabaseMigrations, ForceSyncQueueDriver, ForceArrayMailerDriver;

    /**
     * @var User $administrator
     */
    protected $administrator;


    public function setUp()
    {
        parent::setUp();

        $this->administrator = factory(Invitation::class, 'admin')->create()->user;
    }


    public function testIndexRoute()
    {
        $this->actingAs($this->administrator);
        $this->visitRoute('users.index');

        $this->assertResponseOk()->seeText('User listing')->seeText($this->administrator->name);
    }


    public function testFilterAll()
    {
        /** @var \Illuminate\Support\Collection $invitations */
        $invitations = factory(Invitation::class, 2)->create();
        $invitations = $invitations->merge(factory(Invitation::class, 'banned', 2)->create());
        $invitations = $invitations->merge(factory(Invitation::class, 'admin', 2)->create());

        $this->actingAs($this->administrator);
        $this->visitRoute('users.index');

        foreach ($invitations as $invitation) {
            if ($invitation->user->isBanned()) {
                $this->dontSeeText($invitation->user->username);
            } else {
                $this->seeText($invitation->user->username);
            }
        }
    }


    public function testFilterColleagues()
    {
        $this->markTestIncomplete('Filter colleagues is not implemented yet');
    }


    public function testFilterBanned()
    {
        /** @var \Illuminate\Support\Collection $invitations */
        $invitations = factory(Invitation::class, 2)->create();
        $invitations = $invitations->merge(factory(Invitation::class, 'banned', 2)->create());

        $this->actingAs($this->administrator);
        $this->visitRoute('users.index', ['filter' => 'banned']);

        foreach ($invitations as $invitation) {
            if ($invitation->user->isBanned()) {
                $this->seeText($invitation->user->username);
            } else {
                $this->dontSeeText($invitation->user->username);
            }
        }
    }


    public function testFilterBannedIsVisibleOnlyForAdmin()
    {
        $this->markTestIncomplete('Implement policy for filter banned.');
    }


    public function testFilterAdmin()
    {
        /** @var \Illuminate\Support\Collection $invitations */
        $invitations = factory(Invitation::class, 2)->create();
        $invitations = $invitations->merge(factory(Invitation::class, 'banned', 2)->create());
        $invitations = $invitations->merge(factory(Invitation::class, 'admin', 2)->create());

        $this->actingAs($this->administrator);
        $this->visitRoute('users.index', ['filter' => 'admins']);

        foreach ($invitations as $invitation) {
            if ($invitation->user->is_admin) {
                $this->seeText($invitation->user->username);
            } else {
                $this->dontSeeText($invitation->user->username);
            }
        }
    }


    public function testFilterAdminIsVisibleOnlyForAdmin()
    {
        $this->markTestIncomplete('Implement policy for filter admin.');
    }
}
