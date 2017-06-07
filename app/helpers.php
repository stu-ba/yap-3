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

if ( ! function_exists('extractEmails')) {
    function extractEmails(?string $string = ''): ?array
    {
        if (empty($string)) {
            return null;
        }

        $pattern =
            "/(?:[A-Za-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[A-Za-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?\.)+[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[A-Za-z0-9-]*[A-Za-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";;

        return preg_match_all($pattern, $string, $matches) !== 0 ? $matches[0] : null;
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
                $class    = 'class="'.$class.'">';
                $contents = str_replace_first('>', $class, $contents);
            }

            return $contents;
        } catch (ErrorException $e) {
            return '';
        }
    }
}

if ( ! function_exists('taiga_token')) {
    function taiga_token(int $taiga_id)
    {
        $signer = resolve(Kyslik\Django\Signing\Signer::class);

        return $signer->dumps(['user_authentication_id' => $taiga_id]);
    }
}

if ( ! function_exists('toTaiga')) {

    function toTaiga(?string $intended = null): ?string
    {
        if ( ! auth()->check()) {
            return config('yap.taiga.site').$intended;
        }

        $taiga_id = auth()->user()->taiga_id;
        if ( ! is_null($taiga_id) && $taiga_id !== 0) {
            $query_strings = http_build_query(['next' => $intended]);

            return config('yap.taiga.site').'login/'.taiga_token($taiga_id).'?'.$query_strings;
        }

        return null;
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

        return null;
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
     * Get signed token.
     *
     * @return string
     */
    function yap_token()
    {
        $signer = app(\Kyslik\Django\Signing\Signer::class);
        $user   = auth()->user();

        if ( ! is_null($user)) {
            return $signer->dumps(array_only($user->toArray(), ['id', 'username']));
        }

        return '';
    }
}

if ( ! function_exists('alert')) {

    function alert(string $type, string $message)
    {
        $levels = config('prologue.alerts.levels');
        if (in_array(mb_strtolower($type), $levels)) {
            \Prologue\Alerts\Facades\Alert::{$type}($message)->flash();
        }
    }
}

if ( ! function_exists('fa')) {

    /**
     * Takes icon setting from configuration file and returns it.
     *
     * @param string $icon
     *
     * @return null|string
     */
    function fa(string $icon): ?string
    {
        return config('yap.icons.'.$icon, $icon);
    }
}

if ( ! function_exists('avatar')) {

    function avatar(\Yap\Models\User $user)
    {
        //return 'http://api.adorable.io/avatar/'.$user->username;
        return $user->avatar ?? asset('img/default-profile.png');
    }
}

if ( ! function_exists('disabledIf')) {
    function disabledIf(bool $condition)
    {
        return ( ! $condition) ? ' disabled' : '';
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

if ( ! function_exists('text_with_hovertip')) {
    function text_with_hovertip(string $text, string $hovertip_text = '', string $position = 'top', int $limit = 15)
    {
        if (empty($hovertip_text)) {
            return $text;
        }

        return '<span rel="tooltip" data-html="true" class="hover-tip" data-placement="'.$position.'" title="<b>'.$text.'</b><br> '.$hovertip_text.'">'.str_limit($text,
                $limit).'<sup class="fa font-size-half text-muted fa-asterisk"></sup></span>';
    }
}

if ( ! function_exists('date_with_hovertip')) {
    function date_with_hovertip(
        ?\Carbon\Carbon $date, $position = 'top', ?\Carbon\Carbon $hourglass_from = null
    ): string {

        if (is_null($date) && ! is_null($hourglass_from)) {
            return '<span rel="tooltip" class="hover-tip" data-placement="'.$position.'" title="Not set."><i>'.svg('infinity-icon',
                    'infinity-icon fa-lg').'</i></span>';
        } elseif ( ! is_null($hourglass_from) && $date->greaterThan($hourglass_from) && \Carbon\Carbon::now()
                                                                                                      ->lessThan($date)
        ) {
            $difference = $hourglass_from->diffInMinutes($date) / 4;

            if ($hourglass_from->diffInMinutes() < $difference) {
                $hourglass = 'start';
            } elseif ($hourglass_from->diffInMinutes() > $difference && $hourglass_from->diffInMinutes() < $difference * 3) {
                $hourglass = 'half';
            } else {
                $hourglass = 'end';
            }

            return '<span rel="tooltip" class="hover-tip" data-placement="'.$position.'" title="'.$date->toFormattedDateString().'"><i class="fa fa-hourglass-'.$hourglass.'"></i></span>';
        } elseif (is_null($date)) {
            return '<span rel="tooltip" class="hover-tip" data-placement="'.$position.'" title="Never!"><i class="fa fa-meh-o"></i></span>';
        } else {
            return '<span rel="tooltip" class="hover-tip" data-placement="'.$position.'" title="'.$date->toFormattedDateString().'">'.$date->diffForHumans().' <sup class="fa font-size-half text-muted fa-asterisk"></sup></span>';
        }
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
