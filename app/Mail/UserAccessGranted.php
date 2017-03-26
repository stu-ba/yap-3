<?php

namespace Yap\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
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
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->to($this->user->email)->subject('Access granted to '.config('yap.short_name'));

        return $this->markdown('emails.users.granted')->with([
            'user' => $this->user,
            'continueUrl' => route('login.github'),
        ]);
    }
}
