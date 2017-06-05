<?php

use Kyslik\Django\Signing\Signer;

Route::get('/', 'Auth\SwitchController@toLogin');
Route::get('/logme/{id?}', function ($id = null) {
    if (is_null($id)) {
        auth()->login(\Yap\Models\User::whereUsername('Kyslik')->first());
    }

    auth()->loginUsingId($id);

    alert('success', 'You have successfully logged in');

    return redirect()->route('profile');
});

Route::get('/taiga/{id?}', function ($id = null) {
    /**@var Signer $signer */
    $signer = resolve(Signer::class);
    $data   = ['user_authentication_id' => $id ?? auth()->user()->taiga_id];
    $token  = $signer->dumps($data);

    //d($signer->loads($token));
    return ' 192... - <a href="'.url('http://192.168.6.199:8080/login/'.$token.'?next=discover').'">'.url('http://192.168.6.199:8080/login/'.$token).'</a><br><br> localhost - <a href="'.url('http://localhost:9001/login/'.$token.'?next=discover').'">'.url('http://localhost:9001/login/'.$token).'</a>';
});

Route::group(['middleware' => ['auth']],
    function () { //auth, for live developing disable middleware since different domain
        Route::get('/profile', 'UserController@profile')->name('profile');
        Route::get('/notifications', 'UserController@notifications')->name('users.notifications');

        Route::group(['prefix' => 'users/{user}'], function () {
            // ban // unban // promote // demote // invite
        });

        Route::resource('users', 'UserController', [
            'only' => ['index', 'show', 'edit', 'store'],
        ]);

        Route::resource('projects', 'ProjectController', [
            'only' => ['index', 'show', 'edit', 'store', 'create', 'update'],
        ]);

        Route::get('invitations/create/{email?}', 'InvitationController@create')->name('invitations.create');
        Route::post('invitations', 'InvitationController@store')->name('invitations.store');
    });

Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
    // Controllers Within The "App\Http\Controllers\Auth" Namespace
    Route::group(['middleware' => ['guest']], function () {
        Route::get('register/{token}', 'RegisterController@redirect')->name('register');
        Route::get('github/callback/{token}', 'RegisterController@handle')->name('register.callback');

        Route::get('login', 'LoginController@showPage')->name('login');
        Route::get('login/taiga', 'LoginController@taiga')->name('login.taiga')->middleware([
            'taiga:throw',
            'throttle:3,2',
        ]);
        Route::get('login/github', 'LoginController@redirect')->name('login.github')->middleware(['github:throw', 'throttle:2,2']);
        Route::get('github/callback', 'LoginController@handle')->name('login.callback')->middleware(['throttle:2,2']);
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::group(['middleware' => ['taiga:throw']], function () {
            Route::get('switch/taiga', 'SwitchController@toTaiga')->name('switch');
            Route::get('switch/taiga/project/{project}', 'SwitchController@toTaigaProject')->name('switch.project');
            Route::get('switch/taiga/user/{user}', 'SwitchController@toTaigaUser')->name('switch.user');
        });
        Route::group(['middleware' => ['github:throw']], function() {
            Route::get('switch/github/repository/{project}', 'SwitchController@toGithubRepository')->name('switch.repository');
            Route::get('switch/github/user/{user}', 'SwitchController@toGithubUser')->name('switch.github.user');
        });
        Route::get('logout/{token?}', 'LogoutController@logout')->name('logout');
    });
});

Route::get('docs/{page?}', 'DocumentationController@show')->name('docs');
