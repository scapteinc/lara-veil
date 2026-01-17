<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Lara-Veil
|--------------------------------------------------------------------------
|
| RESTful API endpoints for managing plugins, themes, and media.
| All routes are prefixed with /api and protected with middleware.
|
*/

Route::prefix('api')
    ->middleware(['api'])
    ->group(function () {

        // System API
        Route::prefix('system')
            ->group(function () {
                Route::get('health', function () {
                    return response()->json(['status' => 'ok']);
                })->name('api.system.health');

                Route::get('info', function () {
                    return response()->json([
                        'version' => '2.0.0',
                        'driver' => 'lara-veil',
                    ]);
                })->name('api.system.info');

                Route::get('hooks', function () {
                    $hook = app('hook');
                    return response()->json([
                        'registered' => $hook->getRegistered(),
                        'execution_count' => $hook->getExecutionCount(),
                    ]);
                })->name('api.system.hooks');

                Route::post('cache/clear', function () {
                    cache()->forget('lara-veil.plugins');
                    return response()->json(['message' => 'Cache cleared']);
                })->name('api.system.cache.clear');
            });

        // Plugin API
        Route::prefix('plugins')
            ->group(function () {
                Route::get('/', 'Scapteinc\LaraVeil\Http\Controllers\PluginController@index')
                    ->name('api.plugins.index');

                Route::post('/', 'Scapteinc\LaraVeil\Http\Controllers\PluginController@store')
                    ->name('api.plugins.store');

                Route::get('{id}', 'Scapteinc\LaraVeil\Http\Controllers\PluginController@show')
                    ->name('api.plugins.show');

                Route::put('{id}', 'Scapteinc\LaraVeil\Http\Controllers\PluginController@update')
                    ->name('api.plugins.update');

                Route::delete('{id}', 'Scapteinc\LaraVeil\Http\Controllers\PluginController@destroy')
                    ->name('api.plugins.destroy');

                Route::post('{id}/activate', 'Scapteinc\LaraVeil\Http\Controllers\PluginController@activate')
                    ->name('api.plugins.activate');

                Route::post('{id}/deactivate', 'Scapteinc\LaraVeil\Http\Controllers\PluginController@deactivate')
                    ->name('api.plugins.deactivate');
            });

        // Theme API
        Route::prefix('themes')
            ->group(function () {
                Route::get('/', 'Scapteinc\LaraVeil\Http\Controllers\ThemeController@index')
                    ->name('api.themes.index');

                Route::get('{id}', 'Scapteinc\LaraVeil\Http\Controllers\ThemeController@show')
                    ->name('api.themes.show');

                Route::post('{id}/activate', 'Scapteinc\LaraVeil\Http\Controllers\ThemeController@activate')
                    ->name('api.themes.activate');

                Route::get('{id}/preview', 'Scapteinc\LaraVeil\Http\Controllers\ThemeController@preview')
                    ->name('api.themes.preview');
            });

        // Media API
        Route::prefix('media')
            ->group(function () {
                Route::post('upload', 'Scapteinc\LaraVeil\Http\Controllers\MediaController@upload')
                    ->name('api.media.upload');

                Route::get('{id}', 'Scapteinc\LaraVeil\Http\Controllers\MediaController@show')
                    ->name('api.media.show');

                Route::delete('{id}', 'Scapteinc\LaraVeil\Http\Controllers\MediaController@destroy')
                    ->name('api.media.destroy');

                Route::post('{id}/process', 'Scapteinc\LaraVeil\Http\Controllers\MediaController@process')
                    ->name('api.media.process');

                Route::get('{id}/preview', 'Scapteinc\LaraVeil\Http\Controllers\MediaController@preview')
                    ->name('api.media.preview');
            });
    });
