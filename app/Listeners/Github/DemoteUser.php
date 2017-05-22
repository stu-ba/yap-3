<?php

namespace Yap\Listeners\Github;

use Yap\Events\UserDemoted;

class DemoteUser extends Github
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
        $this->github->removeFromTeam($this->rootTeamId, $event->user->username);
    }
}
