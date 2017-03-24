<?php

namespace Yap\Notifications;

use Illuminate\Notifications\Notification;

class DemotedNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'You have been demoted to basic user.',
            'help_uri' => 'help_uri'
        ];
    }
}
