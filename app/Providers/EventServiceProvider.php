<?php

namespace Yap\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Yap\Events\UserDemoted;
use Yap\Events\UserPromoted;
use Yap\Listeners\Github\DemoteUser as GithubDemote;
use Yap\Listeners\Github\PromoteUser as GithubPromote;
use Yap\Listeners\SendDemotedNotification;
use Yap\Listeners\SendPromotedNotification;
use Yap\Listeners\Taiga\DemoteUser as TaigaDemote;
use Yap\Listeners\Taiga\PromoteUser as TaigaPromote;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserPromoted::class => [
            SendPromotedNotification::class,
            GithubPromote::class,
            TaigaPromote::class,
        ],
        UserDemoted::class => [
            SendDemotedNotification::class,
            GithubDemote::class,
            TaigaDemote::class,
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
