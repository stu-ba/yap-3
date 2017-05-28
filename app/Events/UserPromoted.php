<?php

namespace Yap\Events;

use Illuminate\Queue\SerializesModels;
use Yap\Models\User;

class UserPromoted
{

    use SerializesModels;

    /**
     * @var \Yap\Models\User
     */
    public $user;


    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
