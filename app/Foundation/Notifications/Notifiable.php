<?php

namespace Yap\Foundation\Notifications;

use Illuminate\Notifications\RoutesNotifications;

trait Notifiable
{

    use HasDatabaseNotifications, RoutesNotifications;
}