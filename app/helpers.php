<?php

if (! function_exists('d')) {
    /**
     * @param  mixed
     * @return void
     */
    function d()
    {
        array_map(function ($x) {
            (new \Illuminate\Support\Debug\Dumper())->dump($x);
        }, func_get_args());
    }
}