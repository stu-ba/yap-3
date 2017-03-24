<?php

namespace Yap\Listeners\Taiga;

use Yap\Events\UserPromoted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PromoteUser
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
     * @param  UserPromoted  $event
     * @return void
     */
    public function handle(UserPromoted $event)
    {
        //
    }
}
