<?php

namespace Yap\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Yap\Models\Invitation;

class InvitationProlonged extends Mailable implements ShouldQueue
{

    use SerializesModels;

    /**
     * @var string
     */
    public $queue = 'emails';

    /**
     * @var Invitation
     */
    public $invitation;


    /**
     * Create a new message instance.
     *
     * @param Invitation $invitation
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->to($this->invitation->email)->subject(config('yap.short_name').' confirmation token got prolonged!');

        return $this->markdown('emails.invitations.prolonged')->with([
            'emailHandle' => emailHandle($this->invitation->email),
            'validUntil'  => $this->invitation->valid_until->toDateString(),
            'continueUrl' => route('register', ['token' => $this->invitation->token]),
        ]);
    }
}
