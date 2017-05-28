<?php

namespace Yap\Http\Controllers;

class RouterController extends Controller
{

    public function index($route, $parameters = null)
    {
        if ( ! is_null($parameters)) {
            $parameters = json_decode($parameters, true, 2);
        }

        if (route_exists($route)) {
            return response()->json(['url' => route($route, $parameters, true)]);
        }

        return response()->json([
            'message' => 'Route ['.$route.'] not found.',
        ], 404);
    }
}
