<?php

use Kyslik\Django\Signing\Signer;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/a', function () {
    return abort(503);
});

Route::get('/logme/{id?}', function ($id = null) {
    if (is_null($id)) {
        auth()->login(\Yap\Models\User::whereUsername('Kyslik')->first());
    }

    auth()->loginUsingId($id);

    alert('success', 'You have successfully logged in');

    return redirect()->route('profile');
});

Route::get('/throttle2', function() {
    //return redirect('/');
    return 'got_here';
})->middleware(['throttle:2,2']);

Route::get('/taiga/{id?}', function($id = null) {
    /**@var Signer $signer */
    $signer = resolve(Signer::class);
    $data = ['user_authentication_id' => $id ?? auth()->user()->taiga_id];
    $token = $signer->dumps($data);
    //d($signer->loads($token));
    return ' 192... - <a href="'.url('http://192.168.6.199:8080/login/'.$token.'?next=discover').'">'.url('http://192.168.6.199:8080/login/'.$token).'</a><br><br> localhost - <a href="'.url('http://localhost:9001/login/'.$token.'?next=discover').'">'.url('http://localhost:9001/login/'.$token).'</a>';
});

Route::group(['middleware' => ['auth']], function () { //auth, for live developing disable middleware since different domain
        Route::get('/profile', 'UserController@profile')->name('profile');
        Route::group(['prefix' => 'users/{user}'], function () {
            // ban // unban // promote // demote // invite
            Route::get('ava', 'UserController@availableProjects');
        });

        Route::resource('users', 'UserController', [
            'only' => ['index', 'show', 'edit', 'store'],
        ]);

        //Route::get('/taiga/{id?}', function($id = null) {
        //    /**@var Signer $signer */
        //    $signer = resolve(Signer::class);
        //    $data = ['user_authentication_id' => $id ?? auth()->user()->taiga_id];
        //    $token = $signer->setTimestamp(time() - 20)->dumps($data);
        //    //d($signer->loads($token));
        //    return ' 192... - <a href="'.url('http://192.168.6.199:8080/login/'.$token.'?next=discover').'">'.url('http://192.168.6.199:8080/login/'.$token).'</a><br><br> localhost - <a href="'.url('http://localhost:9001/login/'.$token.'?next=discover').'">'.url('http://localhost:9001/login/'.$token).'</a>';
        //});

        Route::get('invitations/create/{email?}', 'InvitationController@create')->name('invitations.create');
        Route::post('invitations', 'InvitationController@store')->name('invitations.store');
    });

Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
    // Controllers Within The "App\Http\Controllers\Auth" Namespace
    Route::group(['middleware' => ['guest']], function () {
        Route::get('register/{token}', 'RegisterController@redirect')->name('register');
        Route::get('github/callback/{token}', 'RegisterController@handle')->name('register.callback');

        Route::get('login', 'LoginController@showPage')->name('login');
        Route::get('login/taiga', 'LoginController@taiga')->name('login.taiga');
        Route::get('login/github', 'LoginController@redirect')->name('login.github')->middleware(['throttle:2,2']);
        Route::get('github/callback', 'LoginController@handle')->name('login.callback');
    });

    Route::group(['middleware' => ['auth']], function() {
        Route::group(['middleware' => ['taiga:throw']], function() {
            Route::get('switch/taiga', 'SwitchController@toTaiga')->name('switch');
            Route::get('switch/project/{project}', 'SwitchController@toTaigaProject')->name('switch.project');
            Route::get('switch/user/{user}', 'SwitchController@toTaigaUser')->name('switch.user');
        });

        Route::get('switch/repository/{project}', 'SwitchController@toGithubRepository')->name('switch.repository');
        Route::get('logout/{token?}', 'LogoutController@logout')->name('logout');
    });
});

Route::get('docs/{page?}', 'DocumentationController@show')->name('docs');
