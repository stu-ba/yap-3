<?php

namespace Yap\Listeners;

use Yap\Events\UserPromoted;
use Yap\Notifications\PromotedNotification;

class SendPromotedNotification
{

    /**
     * Handle the event.
     *
     * @param UserPromoted $event
     *
     * @return void
     */
    public function handle(UserPromoted $event)
    {
        $event->user->notify(new PromotedNotification());
    }
}
