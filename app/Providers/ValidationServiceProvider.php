<?php

namespace Yap\Providers;

use Illuminate\Support\ServiceProvider;
use Yap\Foundation\Validators\ArrayUnique;
use Yap\Foundation\Validators\NotCurrentUser;
use Yap\Foundation\Validators\RepositoryUnique;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        /** @var \Illuminate\Validation\Factory $validator */
        $validator = resolve(\Illuminate\Validation\Factory::class);
        $validator->extend('repository_unique', RepositoryUnique::class);
        $validator->extend('not_current_user', NotCurrentUser::class);
        $validator->extend('array_unique', ArrayUnique::class);
        $validator->replacer('array_unique', ArrayUnique::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
