<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes for Lara-Veil
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Lara-Veil package.
| These routes are loaded by the LaraVeilServiceProvider.
|
*/

Route::middleware(['web'])
    ->group(function () {
        // Web routes for Lara-Veil can be added here
        // Example: Plugin management dashboard, theme switcher, etc.
    });

/*
|--------------------------------------------------------------------------
| Admin Web Routes for Lara-Veil
|--------------------------------------------------------------------------
|
| Protected admin routes for managing plugins, themes, and media.
| Requires authentication by default.
|
*/

Route::prefix('admin')
    ->middleware(['web', 'auth'])
    ->group(function () {

        // Dashboard
        Route::get('/', 'Scapteinc\LaraVeil\Http\Controllers\Admin\DashboardController@index')
            ->name('lara-veil.admin.dashboard');

        // Plugin Management
        Route::prefix('plugins')
            ->group(function () {
                Route::get('/', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@index')
                    ->name('lara-veil.plugins.index');

                Route::get('create', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@create')
                    ->name('lara-veil.plugins.create');

                Route::post('/', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@store')
                    ->name('lara-veil.plugins.store');

                Route::get('{plugin}/edit', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@edit')
                    ->name('lara-veil.plugins.edit');

                Route::put('{plugin}', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@update')
                    ->name('lara-veil.plugins.update');

                Route::delete('{plugin}', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@destroy')
                    ->name('lara-veil.plugins.destroy');

                Route::post('{plugin}/activate', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@activate')
                    ->name('lara-veil.plugins.activate');

                Route::post('{plugin}/deactivate', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@deactivate')
                    ->name('lara-veil.plugins.deactivate');

                Route::get('{plugin}/settings', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@settings')
                    ->name('lara-veil.plugins.settings');

                Route::put('{plugin}/settings', 'Scapteinc\LaraVeil\Http\Controllers\Admin\PluginManagementController@updateSettings')
                    ->name('lara-veil.plugins.settings.update');
            });

        // Theme Management
        Route::prefix('themes')
            ->group(function () {
                Route::get('/', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@index')
                    ->name('lara-veil.themes.index');

                Route::get('{theme}', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@show')
                    ->name('lara-veil.themes.show');

                Route::post('{theme}/activate', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@activate')
                    ->name('lara-veil.themes.activate');

                Route::get('{theme}/customize', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@customize')
                    ->name('lara-veil.themes.customize');

                Route::put('{theme}/customize', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@updateCustomization')
                    ->name('lara-veil.themes.customize.update');

                Route::get('{theme}/preview', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@preview')
                    ->name('lara-veil.themes.preview');

                Route::get('{theme}/settings', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@settings')
                    ->name('lara-veil.themes.settings');

                Route::put('{theme}/settings', 'Scapteinc\LaraVeil\Http\Controllers\Admin\ThemeManagementController@updateSettings')
                    ->name('lara-veil.themes.settings.update');
            });

        // Media Management
        Route::prefix('media')
            ->group(function () {
                Route::get('/', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@index')
                    ->name('lara-veil.media.index');

                Route::get('create', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@create')
                    ->name('lara-veil.media.create');

                Route::get('upload', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@uploadForm')
                    ->name('lara-veil.media.upload');

                Route::post('/', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@store')
                    ->name('lara-veil.media.store');

                Route::get('{media}', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@show')
                    ->name('lara-veil.media.show');

                Route::delete('{media}', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@destroy')
                    ->name('lara-veil.media.destroy');

                Route::get('{media}/edit', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@edit')
                    ->name('lara-veil.media.edit');

                Route::put('{media}', 'Scapteinc\LaraVeil\Http\Controllers\Admin\MediaManagementController@update')
                    ->name('lara-veil.media.update');
            });

        // Settings
        Route::prefix('settings')
            ->group(function () {
                Route::get('/', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SettingsController@index')
                    ->name('lara-veil.settings.index');

                Route::get('general', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SettingsController@general')
                    ->name('lara-veil.settings.general');

                Route::put('general', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SettingsController@updateGeneral')
                    ->name('lara-veil.settings.general.update');

                Route::get('security', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SettingsController@security')
                    ->name('lara-veil.settings.security');

                Route::put('security', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SettingsController@updateSecurity')
                    ->name('lara-veil.settings.security.update');

                Route::get('cache', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SettingsController@cache')
                    ->name('lara-veil.settings.cache');

                Route::post('cache/clear', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SettingsController@clearCache')
                    ->name('lara-veil.settings.cache.clear');
            });

        // System
        Route::prefix('system')
            ->group(function () {
                Route::get('info', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SystemController@info')
                    ->name('lara-veil.system.info');

                Route::get('hooks', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SystemController@hooks')
                    ->name('lara-veil.system.hooks');

                Route::get('diagnostics', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SystemController@diagnostics')
                    ->name('lara-veil.system.diagnostics');

                Route::get('logs', 'Scapteinc\LaraVeil\Http\Controllers\Admin\SystemController@logs')
                    ->name('lara-veil.system.logs');
            });
    });
