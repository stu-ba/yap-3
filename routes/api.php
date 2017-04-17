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

Route::post('/users/invite', 'InvitationController@store')->middleware('auth:api');
Route::get('/router/{route}/{parameters?}', function ($route, $parameters = null) {

    if ( ! is_null($parameters)) {
        $parameters = json_decode($parameters, true, 2);
    }

    if (route_exists($route)) {
        return route($route, $parameters, true);
    }

    return abort(404);

})->middleware('auth:api');
