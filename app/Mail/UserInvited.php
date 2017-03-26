<?php

namespace Yap\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Yap\Models\Invitation;

class UserInvited extends Mailable implements ShouldQueue
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
     *
     * @internal param User $user
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->subject('Invitation to ' . config('yap.short_name'));
        $this->to($invitation->email);
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.users.invited')->with([
                'emailHandle' => emailHandle($this->invitation->email),
                'validUntil' => $this->validUntil(),
                'continueUrl' => route('register', ['token' => $this->invitation->token])
            ]);
    }


    /**
     * @return null|string
     */
    private function validUntil(): ?string
    {
        return is_null($this->invitation->valid_until) ? null : $this->invitation->valid_until->toDateString();
    }

}
