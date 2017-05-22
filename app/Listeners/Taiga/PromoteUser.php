<?php

namespace Yap\Listeners\Taiga;

use Yap\Events\UserPromoted;

class PromoteUser extends Taiga
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
        $this->taiga->roleChange($event->user);
    }
}
