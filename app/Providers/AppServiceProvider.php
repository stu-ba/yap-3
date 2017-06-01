<?php

namespace Yap\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('array_unique', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) !== 1) {
                throw new \InvalidArgumentException('Validation rule needs parameter.');
            }

            $dataOfSecondArray = $validator->getData()[$parameters[0]] ?? [];

            if (empty($dataOfSecondArray)) {
                return true;
            }

            $intersection = array_intersect($value, $dataOfSecondArray);
            if (count($intersection) === 0) {
                return true;
            }

            return false;
        });

        \Validator::replacer('array_unique', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':parameter', $parameters[0], $message);
        });
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
