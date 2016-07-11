<?php
/**
 * Admin routes
 */

Route::group(['prefix' => config('admin.prefix', 'admin'), 'middleware' => ['web'], 'namespace' => 'OroCMS\Admin\Controllers'], function () {
    Route::group(['middleware' => config('admin.filter.guest')], function () {
        Route::resource('login', 'AuthController', [
            'only' => ['index', 'store'],
            'names' => [
                'index' => 'admin.login.index',
                'store' => 'admin.login.store',
            ],
        ]);
    });

    Route::group(['middleware' => config('admin.filter.auth')], function () {
        Route::get('/', ['as' => 'admin.dashboard', 'uses' => 'DashboardController@index']);
        Route::get('/logout', ['as' => 'admin.logout', 'uses' => 'AuthController@logout']);

        Route::group(['prefix' => 'users'], function() {
            Route::get('/', ['as' => 'admin.users.index', 'uses' => 'UsersController@index']);
            Route::get('/create', ['as' => 'admin.users.create', 'uses' => 'UsersController@create']);
            Route::get('/{users}/edit', ['as' => 'admin.users.edit', 'uses' => 'UsersController@edit']);
            Route::post('/', ['as' => 'admin.users.store', 'uses' => 'UsersController@store']);
            Route::put('/{users}', ['as' => 'admin.users.update', 'uses' => 'UsersController@update']);
            Route::patch('/{users?}', ['as' => 'admin.users.restore', 'uses' => 'UsersController@restore']);
            Route::delete('/{users?}', ['as' => 'admin.users.remove', 'uses' => 'UsersController@destroy']);
        });

        Route::get('/modules', ['as' => 'admin.modules.index', 'uses' => 'ModulesController@index']);
        Route::put('/modules', ['as' => 'admin.modules.update', 'uses' => 'ModulesController@update']);
        Route::get('/plugins', ['as' => 'admin.plugins.index', 'uses' => 'PluginsController@index']);
        Route::put('/plugins', ['as' => 'admin.plugins.update', 'uses' => 'PluginsController@update']);

        // settings
        Route::get('/settings', ['as' => 'admin.settings.index', 'uses' => 'SettingsController@index']);
        Route::put('/settings', ['as' => 'admin.settings.update', 'uses' => 'SettingsController@update']);

        // profile details
        Route::get('/profile', ['as'=>'admin.profile', 'uses'=>'AuthController@show_profile']);
        Route::put('/profile', ['as'=>'admin.profile', 'uses'=>'AuthController@update_profile']);

        Route::get('logs', ['as' => 'admin.logs.index', 'uses' => 'LogsController@index']);
    });
});
