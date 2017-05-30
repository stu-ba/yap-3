<?php

namespace Yap\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory as ViewFactory;

class ComposerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        resolve(ViewFactory::class)->composer(['layouts.yap'], function ($view) {
            $view->with('unreadNotificationsCount', auth()->user()->unreadNotifications()->count());
        });
    }
}
