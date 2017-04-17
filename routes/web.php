<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('/a', function () {
    return abort(500);
});

Route::get('/logme', function () {
   auth()->login(\Yap\Models\User::whereUsername('Kyslik')->first());
   return redirect()->route('users.index');
});

Route::get('home', function () {
    d('logged-in:', Auth::user(), Cookie::get('github_token'));
})->name('home')->middleware('auth');

Route::group(['middleware' => ['auth']], function () { //auth, for live developing disable middleware since different domain
    Route::get('/profile', 'UserController@profile')->name('profile');
    Route::group(['prefix' => 'users'], function () {
        // ban // unban // promote // demote // invite
    });

    Route::resource('users', 'UserController', ['only' => [
        'index', 'show', 'edit', 'update', 'store'
    ]]);

    Route::get('invitations/create/{email?}', 'InvitationController@create')->name('invitations.create');
    Route::post('invitations', 'InvitationController@store')->name('invitations.store');
});

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