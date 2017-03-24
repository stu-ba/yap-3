<?php

namespace Yap\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Yap\Listeners\SendDemotedNotification;
use Yap\Listeners\SendPromotedNotification;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Yap\Events\UserPromoted' => [
            SendPromotedNotification::class,
            'Yap\Listeners\Github\PromoteUser',
            'Yap\Listeners\Taiga\PromoteUser'
        ],
        'Yap\Events\UserDemoted' => [
            SendDemotedNotification::class,
            'Yap\Listeners\Github\DemoteUser',
            'Yap\Listeners\Taiga\DemoteUser'
        ],
    ];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
