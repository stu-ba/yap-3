<?php


Route::get('/', function () {
    return view('welcome');
});

Route::get('home', function() {
    d('logged-in:', Auth::user(), Cookie::get('github_token'));
})->name('home')->middleware('auth');

Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
    // Controllers Within The "App\Http\Controllers\Auth" Namespace
    Route::group(['middleware' => ['guest']], function() {
        Route::get('register/{token}', 'RegisterController@register');
        Route::get('github/callback/{token}', 'RegisterController@handle');

        Route::get('login', 'LoginController@showPage')->name('login');
        Route::get('login/github', 'LoginController@login')->name('login.github')->middleware(['throttle:5,1']);
        Route::get('github/callback', 'LoginController@handle');
    });

    Route::get('logout', 'LogoutController@logout')->name('logout')->middleware(['auth']);
});