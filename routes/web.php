<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/l', function () {
    return view('auth.login');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function() {
    dd(Auth::user());
})->name('home');

Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'middleware' => ['guest']], function () {
    // Controllers Within The "App\Http\Controllers\Auth" Namespace
    Route::get('register/{token}', 'RegisterController@register');
    //Route::get('register', 'RegisterController@showRegisterForm');
    //Route::post('register', );


    Route::get('login/github', 'LoginController@redirectToGithub')->name('login');
    Route::get('login/github/callback/{token?}', 'LoginController@handleGithubCallback');
});