<?php

namespace Yap\Listeners\Github;

use Yap\Events\UserDemoted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DemoteUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserDemoted  $event
     * @return void
     */
    public function handle(UserDemoted $event)
    {
        //
    }
}
