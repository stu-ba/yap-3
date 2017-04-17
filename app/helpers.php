<?php

if ( ! function_exists('d')) {
    function d()
    {
        array_map(function ($x) {
            (new \Illuminate\Support\Debug\Dumper())->dump($x);
        }, func_get_args());
    }
}

if ( ! function_exists('emailHandle')) {
    function emailHandle(string $e): string
    {
        return substr($e, 0, strrpos($e, '@'));
    }
}

if ( ! function_exists('is_email')) {
    function is_email(string $e): bool
    {
        return (bool)filter_var($e, FILTER_VALIDATE_EMAIL);
    }
}

if ( ! function_exists('svg')) {
    function svg(string $src, string $class = null)
    {
        try {
            $contents = trim(preg_replace('/\s+/', ' ', file_get_contents(public_path('svg/'.$src.'.svg'))));
            if ( ! is_null($class)) {
                $class = 'class="'.$class.'">';
                $contents = str_replace_first('>', $class, $contents);
            }

            return $contents;
        } catch (ErrorException $e) {
            return '';
        }
    }
}

if ( ! function_exists('systemAccount')) {
    function systemAccount()
    {
        return Cache::rememberForever('system_account', function () {
            return app(Yap\Models\User::class)->system() ?? factory(Yap\Models\User::class, 'system')->create();
        });
    }
}

if ( ! function_exists('markdown')) {
    function markdown(string $text)
    {
        return (new \Yap\Auxiliary\BlockQuoteParser)->text($text);
    }
}

if ( ! function_exists('set_active_paths')) {

    /**
     * @param string|array $paths
     * @param string       $active
     *
     * @return string
     */
    function set_active_paths($paths, string $active = 'active')
    {
        if ( ! is_array($paths)) {
            $paths = (array)$paths;
        }

        foreach ($paths as $path) {
            if (call_user_func_array('Request::is', (array)$path)) {
                return $active;
            }
        }
    }
}

if ( ! function_exists('set_active_routes')) {

    /**
     * @param string|array $routes
     * @param string       $output
     *
     * @return string
     */
    function set_active_routes($routes, string $output = 'active'): ?string
    {
        if ( ! is_array($routes)) {
            $routes = (array)$routes;
        }

        foreach ($routes as $route) {
            if (Route::is($route)) {
                return $output;
            }
        }
    }
}

if ( ! function_exists('set_active_filter')) {
    function set_active_filter(string $filter = 'all', array $ignore = []): ?string
    {
        $current = request()->get('filter');

        if (strcasecmp($filter, $current) === 0) {
            return 'active';
        }

        foreach ($ignore as $item) {
            if (is_string($item) && strcasecmp($current, $item) === 0) {
                return '';
            }
        }

        return null;
    }
}

if ( ! function_exists('yap_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function yap_token()
    {
        $signer = app(\Kyslik\Django\Signing\Signer::class);
        $user = auth()->user();

        if ( ! is_null($user)) {
            return $signer->setMaxAge(5)->dumps(array_only($user->toArray(), ['id', 'username']));
        }

        return [];
    }
}

if ( ! function_exists('route_exists')) {
    /**
     * @param $route
     *
     * @return bool
     */
    function route_exists(string $route = null): bool
    {
        $routes = \Route::getRoutes()->getRoutes();
        foreach ($routes as $r) {
            if ($r->getName() === $route) {
                return true;
            }
        }

        return false;
    }
}

if ( ! function_exists('date_with_hovertip')) {
    function date_with_hovertip(\Carbon\Carbon $date, $position = 'top'): string
    {
        return '<span rel="tooltip" class="hover-tip" data-placement="'.$position.'" title="'.$date->toFormattedDateString().'">'.$date->diffForHumans().'</span>';
    }
}

if ( ! function_exists('in_range')) {
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
