<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/invitations', 'InvitationController@store')->middleware('auth:api')->name('api.invitations.store');
Route::get('/router/{route}/{parameters?}', function ($route, $parameters = null) {

    if ( ! is_null($parameters)) {
        $parameters = json_decode($parameters, true, 2);
    }

    if (route_exists($route)) {
        return response()->json(['url' => route($route, $parameters, true)]);
    }

    return response()->json([
        'message' => 'Route ['.$route.'] not found.',
    ], 404);

})->middleware('auth:api');
