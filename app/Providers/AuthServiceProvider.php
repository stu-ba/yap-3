<?php

namespace Yap\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Kyslik\Django\Signing\Signer;
use Yap\Foundation\Auth\TokenGuard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'Yap\Model' => 'Yap\Policies\ModelPolicy',
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
            $guard = new TokenGuard(
                $this->app['auth']->createUserProvider($config['provider']),
                $this->app['request'],
                $this->app->make(Signer::class)
            );

            $this->app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
