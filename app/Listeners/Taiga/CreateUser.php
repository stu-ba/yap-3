<?php

namespace Yap\Listeners\Taiga;

use Yap\Events\UserConfirmed;

class CreateUser extends Taiga
{

    /**
     * Handle the event.
     *
     * @param  UserConfirmed $event
     *
     * @return void
     */
    protected function handle(UserConfirmed $event)
    {
        /** @var \Yap\Models\User $user */
        $user      = $event->user;
        $taigaUser = $this->taiga->createUser($user);
        $user->update(['taiga_id' => $taigaUser->id]);

        if ($user->is_admin) {
            $user->promote(true);
        }
    }
}
