<?php

namespace Yap\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Yap\Events\ProjectCreated;
use Yap\Events\RepositoryRequested;
use Yap\Events\TeamRequested;
use Yap\Events\UserBanned;
use Yap\Events\UserConfirmed;
use Yap\Events\UserDemoted;
use Yap\Events\UserPromoted;
use Yap\Events\UserUnbanned;
use Yap\Listeners\Github\CreateRepository;
use Yap\Listeners\Github\CreateTeam;
use Yap\Listeners\Github\DemoteUser as GithubDemote;
use Yap\Listeners\Github\PromoteUser as GithubPromote;
use Yap\Listeners\SendDemotedNotification;
use Yap\Listeners\SendPromotedNotification;
use Yap\Listeners\Taiga\ChangeUserRole;
use Yap\Listeners\Taiga\CreateProject;
use Yap\Listeners\Taiga\CreateUser;

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
            ChangeUserRole::class,
        ],

        UserDemoted::class => [
            SendDemotedNotification::class,
            GithubDemote::class,
            ChangeUserRole::class,
        ],

        UserBanned::class => [

        ],

        UserUnbanned::class => [

        ],

        UserConfirmed::class => [
            CreateUser::class,
        ],

        ProjectCreated::class => [
            CreateProject::class,
        ],

        TeamRequested::class => [
            CreateTeam::class,
        ],

        RepositoryRequested::class => [
            CreateRepository::class,
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
