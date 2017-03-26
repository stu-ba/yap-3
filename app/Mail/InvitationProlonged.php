<?php

namespace Yap\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $this->subject(config('yap.short_name') . ' confirmation token got prolonged!');
        $this->to($invitation->email);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.invitations.prolonged')->with([
            'emailHandle' => emailHandle($this->invitation->email),
            'validUntil' => $this->invitation->valid_until->toDateString(),
            'continueUrl' => route('register', ['token' => $this->invitation->token])
        ]);
    }
}
