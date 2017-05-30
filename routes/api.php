<?php

Route::group(['middleware' => 'auth:yap'], function () {
    Route::get('/router/{route}/{parameters?}', 'RouterController@index')->name('api.router');
    Route::post('/invitations', 'InvitationController@store')->name('api.invitations.store');

    Route::group(['prefix' => '/users/{user}'], function () {
        Route::patch('/ban', 'UserController@ban')->name('api.users.ban');
        Route::patch('/unban', 'UserController@unban')->name('api.users.unban');
        Route::patch('/promote', 'UserController@promote')->name('api.users.promote');
        Route::patch('/demote', 'UserController@demote')->name('api.users.demote');
        Route::get('/available-projects', 'UserController@availableProjects')->name('api.users.available.projects');
        Route::get('/projects', 'UserController@projectList')->name('api.users.projects');
    });

    Route::group(['prefix' => '/projects/{project}'], function () {
        Route::delete('/users/{user}', 'ProjectController@removeUser')->name('api.projects.remove.user');
        Route::post('/users/{user}', 'ProjectController@addUser')->name('api.projects.add.user');
        Route::patch('/users/{user}', 'ProjectController@makeLeader')->name('api.projects.make.user.leader');
        Route::patch('/users/{user}', 'ProjectController@makeParticipant')->name('api.projects.make.user.participant');
    });
});
