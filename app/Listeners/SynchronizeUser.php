<?php

namespace Yap\Listeners;

use Yap\Events\UserBanned;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yap\Events\UserUnbanned;

class SynchronizeUser implements ShouldQueue
{
    //use DelayJob;

    /**
     * Handle the event.
     *
     * @param  UserBanned|UserUnbanned $event
     * @return void
     */
    public function handle($event)
    {
        /** @var \Yap\Models\User $user */
        $user = $event->user;

        if ($user->isBanned()) {

        }

    }
}
