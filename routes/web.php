<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('/a', function () {
    return abort(500);
});

Route::get('home', function () {
    d('logged-in:', Auth::user(), Cookie::get('github_token'));
})->name('home')->middleware('auth');

Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
    // Controllers Within The "App\Http\Controllers\Auth" Namespace
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', function () {
            return redirect()->route('login');
        });
        Route::get('register/{token}', 'RegisterController@redirect')->name('register');
        Route::get('github/callback/{token}', 'RegisterController@handle')->name('register.callback');

        Route::get('login', 'LoginController@showPage')->name('login');
        Route::get('login/github', 'LoginController@redirect')->name('login.github')->middleware(['throttle:2,2']);
        Route::get('github/callback', 'LoginController@handle')->name('login.callback');
    });

    Route::get('logout', 'LogoutController@logout')->name('logout')->middleware(['auth']);
    Route::get('/', function () {
        return redirect()->route('home');
    })->middleware('auth');
});

Route::get('docs/{page?}', 'DocumentationController@show')->name('docs');