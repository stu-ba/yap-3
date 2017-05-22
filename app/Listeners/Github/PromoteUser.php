<?php

namespace Yap\Listeners\Github;

use Yap\Events\UserPromoted;

class PromoteUser extends Github
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
        $this->github->addToTeam($this->rootTeamId, $event->user->username);
    }
}
