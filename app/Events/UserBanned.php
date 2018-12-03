<?php

namespace Yap\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserBanned
{

    use Dispatchable, SerializesModels;

    /**
     * @var \Yap\Models\User
     */
    public $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
