<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\ForceArrayMailerDriver;
use Tests\ForceSyncQueueDriver;
use Tests\TestCase;
use Yap\Models\Invitation;
use Yap\Models\User;

class InvitationsTest extends TestCase
{

    use DatabaseMigrations, ForceSyncQueueDriver, ForceArrayMailerDriver;

    /**
     * @var User $administrator
     */
    protected $administrator;

    /**
     * @var array $data
     */
    protected $data;


    public function setUp()
    {
        parent::setUp();

        $this->administrator = factory(Invitation::class, 'admin')->create()->user;
        $this->data          = ['email' => 'johnny.bravo@cartoon.net'];
    }


    // Tests for API, consumed by applications JavaScript

    public function testInvitationCreationUsingApi()
    {
        $this->withoutMiddleware();
        $this->actingAs($this->administrator, 'yap');

        $response = $this->postXMLHttp(route('api.invitations.store'), $this->data);

        $response->assertStatus(200)->seeJson([
            'success' => true,
        ]);
    }


    public function testInvitationCreationUsingApiFailsWithoutEmail()
    {
        $this->withoutMiddleware();
        $this->actingAs($this->administrator, 'yap');

        $response = $this->postXMLHttp(route('api.invitations.store'), []);

        $response->assertStatus(422)->seeJsonContains(['The email field is required.']);
    }


    public function testInvitationCreationUsingApiFailsWithInvalidEmail()
    {
        $this->withoutMiddleware();
        $this->actingAs($this->administrator, 'yap');

        $response = $this->postXMLHttp(route('api.invitations.store'), ['email' => 'johnny.bravo']);

        $response->assertStatus(422)->seeJsonContains(['The email must be a valid email address.']);
    }


    public function testInvitationCreationUsingApiFailsWithNonUniqueEmail()
    {
        $invitation      = factory(Invitation::class)->create();
        $invitationEmail = $invitation->email;
        $userEmail       = $invitation->user->email;

        $this->withoutMiddleware();
        $this->actingAs($this->administrator, 'yap');

        $response = $this->postXMLHttp(route('api.invitations.store'), ['email' => $invitationEmail]);
        $response->assertStatus(422)->seeJsonContains(['Invitation with this email already exists.']);

        $response = $this->postXMLHttp(route('api.invitations.store'), ['email' => $userEmail]);
        $response->assertStatus(422)->seeJsonContains(['Invitation with this email already exists.']);
    }


    public function testInvitationCreationUsingApiIsAvailableOnlyToAdmins()
    {
        $basic = $invitation = factory(Invitation::class, 'admin')->create()->user;
        $this->withoutMiddleware();
        $this->actingAs($basic, 'yap');

        $response = $this->postXMLHttp(route('api.invitations.store'), $this->data);
        $this->markTestIncomplete('Implementation required');
        $response->assertStatus(403);
    }


    // Test HTML version of invitation creation

    public function testInvitationCreationRouteAndRecentInvitations()
    {
        $invitations        = factory(Invitation::class, 'empty', 5)->create();
        $invitationConsumed = factory(Invitation::class)->create();

        $this->actingAs($this->administrator);
        $this->visitRoute('invitations.create');
        $this->see('Create invitation');
        $this->see('Invitations listing');

        $this->dontSee($invitationConsumed->email);

        foreach ($invitations as $invitation) {
            $this->see($invitation->email);
        }
    }


    public function testInvitationCreationIsAvailableOnlyToAdmins()
    {
        $this->markTestIncomplete('Implementation required');

        $basic = factory(Invitation::class)->create()->user;
        $this->actingAs($basic);
        $this->visitRoute('invitations.create');
    }


    public function testInvitationCreationDisplaysAlertCreated()
    {
        $this->actingAs($this->administrator);
        $this->visitRoute('invitations.create');

        $response = $this->submitForm('Invite', $this->data);
        $response->see('Invitation for potential user with email \''.$this->data['email'].'\' was created.');
    }


    public function testInvitationCreationDisplaysAlertProlonged()
    {
        factory(Invitation::class, 'empty')->create(['email' => $this->data['email']]);
        $this->actingAs($this->administrator);
        $this->visitRoute('invitations.create');

        $response = $this->submitForm('Invite', $this->data);
        $response->see('Invitation for potential user with email \''.$this->data['email'].'\' was prolonged.');
    }


    public function testInvitationCreationDisplaysAlertAlreadyConfirmed()
    {
        factory(Invitation::class)->create(['email' => $this->data['email']]);
        $this->actingAs($this->administrator);
        $this->visitRoute('invitations.create');

        $response = $this->submitForm('Invite', $this->data);
        $response->see('User with email \''.$this->data['email'].'\' is already confirmed.');
    }


    public function testInvitationCreationDisplaysAlertValidForever()
    {
        factory(Invitation::class, 'empty')->create(['email' => $this->data['email']]);
        $this->actingAs($this->administrator);
        $this->visitRoute('invitations.create');

        $response = $this->submitForm('Invite', array_merge($this->data, ['indefinite' => 'true']));
        $response->see('Invitation for potential user with email \''.$this->data['email'].'\' is now valid forever.');
    }


    public function testInvitationCreationDisplaysAlertBanned()
    {
        factory(Invitation::class, 'banned')->create(['email' => $this->data['email']]);
        $this->actingAs($this->administrator);
        $this->visitRoute('invitations.create');

        $response = $this->submitForm('Invite', $this->data);
        $response->see('User with email \''.$this->data['email'].'\' is banned.');
    }
}
