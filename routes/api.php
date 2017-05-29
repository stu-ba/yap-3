<?php

Route::group(['middleware' => 'auth:yap'], function() {
    Route::get('/router/{route}/{parameters?}', 'RouterController@index')->name('api.router');
    Route::post('/invitations', 'InvitationController@store')->name('api.invitations.store');

    Route::group(['prefix' => '/users/{user}'], function () {
        Route::post('/ban', 'UserController@ban')->name('api.users.ban');
        Route::post('/unban', 'UserController@unban')->name('api.users.unban');
        Route::post('/promote', 'UserController@promote')->name('api.users.promote');
        Route::post('/demote', 'UserController@demote')->name('api.users.demote');
    });
});
