<?php

namespace Yap\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Yap\Models\Invitation;

class InvitationUrged extends Mailable implements ShouldQueue
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
        $this->to($this->invitation->email)->subject('Confirm your '.config('yap.short_name').' account');

        return $this->markdown('emails.invitations.urge')->with([
            'emailHandle' => emailHandle($this->invitation->email),
            'continueUrl' => route('register', ['token' => $this->invitation->token]),
        ]);
    }
}
