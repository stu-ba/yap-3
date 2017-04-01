<?php

if (! function_exists('d')) {
    function d()
    {
        array_map(function ($x) {
            (new \Illuminate\Support\Debug\Dumper())->dump($x);
        }, func_get_args());
    }
}

if (! function_exists('emailHandle')) {
    function emailHandle(string $e): string
    {
        return substr($e, 0, strrpos($e, '@'));
    }
}

if (! function_exists('is_email')) {
    function is_email(string $e): bool
    {
        return (bool) filter_var($e, FILTER_VALIDATE_EMAIL);
    }
}

if (! function_exists('svg')) {
    function svg(string $src)
    {
        try {
            return trim(preg_replace('/\s+/', ' ', file_get_contents(public_path('svg/'.$src.'.svg'))));
        } catch (ErrorException $e) {
            return '';
        }
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

if (! function_exists('systemAccount')) {
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

if (! function_exists('markdown')) {
    function markdown(string $text) {
        return (new \Yap\Auxiliary\BlockQuoteParser)->text($text);
    }
}

if (! function_exists('in_range')) {
    /**
     * Determines if $number is between $min and $max.
     *
     * @param int  $number    The number to test
     * @param int  $min       The minimum value in the range
     * @param int  $max       The maximum value in the range
     * @param bool $inclusive Whether the range should be inclusive or not
     *
     * @return bool Whether the number was in the range
     */
    function in_range($number, $min, $max, $inclusive = false)
    {
        if (is_int($number) && is_int($min) && is_int($max)) {
            return $inclusive ? ($number >= $min && $number <= $max) : ($number > $min && $number < $max);
        }

        return false;
    }
}
