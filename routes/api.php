<?php

Route::group(['middleware' => 'auth:yap'], function() {
    Route::post('/invitations', 'InvitationController@store')->name('api.invitations.store');
    Route::get('/router/{route}/{parameters?}', 'RouterController@index')->name('api.router');
});
