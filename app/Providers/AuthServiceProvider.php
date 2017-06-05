<?php

namespace Yap\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Kyslik\Django\Signing\Signer;
use Yap\Foundation\Auth\YapGuard;
use Yap\Models\Invitation;
use Yap\Models\Project;
use Yap\Models\User;
use Yap\Policies\InvitationPolicy;
use Yap\Policies\ProjectPolicy;
use Yap\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class       => UserPolicy::class,
        Project::class    => ProjectPolicy::class,
        Invitation::class => InvitationPolicy::class,
    ];


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('yap', function ($app, $name, array $config) {
            $guard = new YapGuard($app['auth']->createUserProvider($config['provider']), $app['request'],
                $app->make(Signer::class));

            $this->app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
