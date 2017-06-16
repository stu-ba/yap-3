<?php

namespace Yap\Listeners\Taiga;

use Yap\Events\UserDemoted;
use Yap\Events\UserPromoted;

class ChangeUserRole extends Taiga
{
    /**
     * Pre-condition, reschedule if condition is not met.
     *
     * @param UserPromoted|UserDemoted $event
     *
     * @return bool
     */
    public function check($event): bool
    {
        return $this->checker->check() && ! is_null($event->user->taiga_id);
    }


    /**
     * Handle the event.
     *
     * @param UserPromoted|UserDemoted $event
     *
     * @return void
     */
    protected function handle($event)
    {
        $this->taiga->roleChange($event->user);
    }
}
