<?php

namespace Yap\Foundation\Notifications;

trait HasDatabaseNotifications
{
    use \Illuminate\Notifications\HasDatabaseNotifications {
        \Illuminate\Notifications\HasDatabaseNotifications::notifications as parentNotifications;
    }

    /**
     * Get the entity's notifications.
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
                    ->orderBy('created_at', 'desc');
    }
}
