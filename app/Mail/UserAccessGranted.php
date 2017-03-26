<?php

namespace Yap\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yap\Models\User;

class UserAccessGranted extends Mailable implements ShouldQueue
{

    use SerializesModels;

    /**
     * @var string
     */
    public $queue = 'emails';

    /**
     * @var User
     */
    public $user;


    /**
     * Create a new message instance.
     *
     * @param User $user
     *
     * @internal param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->subject('Access granted to ' . config('yap.short_name'));
        $this->to($user->email);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.users.granted')->with([
            'user' => $this->user,
            'continueUrl' => route('login.github')
        ]);
    }
}
