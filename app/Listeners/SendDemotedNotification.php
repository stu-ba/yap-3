<?php

namespace Yap\Listeners;

use Yap\Events\UserDemoted;
use Yap\Notifications\DemotedNotification;

class SendDemotedNotification
{
    /**
     * Handle the event.
     *
     * @param UserDemoted $event
     *
     * @return void
     */
    public function handle(UserDemoted $event)
    {
        $event->user->notify(new DemotedNotification());
    }
}
