<?php

if ( ! function_exists('d')) {
    /**
     * @param  mixed
     *
     * @return void
     */
    function d()
    {
        array_map(function ($x) {
            (new \Illuminate\Support\Debug\Dumper())->dump($x);
        }, func_get_args());
    }
}

//if ( ! function_exists('systemAccount')) {
//    function systemAccount()
//    {
//        $user = resolve(Yap\Models\User::class);
//
//        return $user->system() ?? factory(Yap\Models\User::class, 'system')->create();
//    }
//}

if ( ! function_exists('systemAccount')) {
    function systemAccount()
    {
        //if ( ! app()->bound('system_account')) {
        //    app()->singleton('system_account', function () {
        //        return app(Yap\Models\User::class)->system();
        //    });
        //}

        //return app('system_account');

        return Cache::rememberForever('system_account', function () {
            return app(Yap\Models\User::class)->system() ?? factory(Yap\Models\User::class, 'system')->create();
        });


    }
}

if ( ! function_exists('in_range')) {
    /**
     * Determines if $number is between $min and $max
     *
     * @param  integer $number    The number to test
     * @param  integer $min       The minimum value in the range
     * @param  integer $max       The maximum value in the range
     * @param  boolean $inclusive Whether the range should be inclusive or not
     *
     * @return boolean              Whether the number was in the range
     */
    function in_range($number, $min, $max, $inclusive = false)
    {
        if (is_int($number) && is_int($min) && is_int($max)) {
            return $inclusive ? ($number >= $min && $number <= $max) : ($number > $min && $number < $max);
        }

        return false;
    }
}
