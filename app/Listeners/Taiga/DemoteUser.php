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
    public function handle(UserDemoted $event)
    {
        $this->roleChange($event->user);
    }
}
