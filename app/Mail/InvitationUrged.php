<?php

namespace Yap\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $this->subject('Confirm your ' . config('yap.short_name') . ' account');
        $this->to($invitation->email);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.invitations.urge')->with([
            'emailHandle' => emailHandle($this->invitation->email),
            'continueUrl' => route('register', ['token' => $this->invitation->token])
        ]);
    }
}
