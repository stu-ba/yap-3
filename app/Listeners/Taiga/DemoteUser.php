<?php

namespace Yap\Listeners\Taiga;

use Yap\Events\UserDemoted;

class DemoteUser extends Taiga
{

    /**
     * Handle the event.
     *
     * @param UserDemoted $event
     *
     * @return void
     */
    protected function handle(UserDemoted $event)
    {
        $this->taiga->roleChange($event->user);
    }
}
