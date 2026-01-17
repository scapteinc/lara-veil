<?php

namespace Scapteinc\LaraVeil\Providers;

use Illuminate\Support\ServiceProvider;
use Scapteinc\LaraVeil\Core\PluginManager;
use Scapteinc\LaraVeil\Core\ThemeManager;
use Scapteinc\LaraVeil\Core\HookSystem;
use Scapteinc\LaraVeil\Services\Vrm\MediaForgeService;

class LaraVeilServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/lara-veil.php',
            'lara-veil'
        );

        // Register core services
        $this->registerHookSystem();
        $this->registerPluginManager();
        $this->registerThemeManager();
        $this->registerMediaForge();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/lara-veil.php' => config_path('lara-veil.php'),
            __DIR__ . '/../../config/vormia.php' => config_path('vormia.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Register console commands here
            ]);
        }

        // Execute system init hook
        $this->app['hook']->doAction('system.init');
    }

    /**
     * Register the hook system.
     */
    protected function registerHookSystem(): void
    {
        $this->app->singleton('hook', function () {
            return new HookSystem();
        });
    }

    /**
     * Register the plugin manager.
     */
    protected function registerPluginManager(): void
    {
        $this->app->singleton('plugin.manager', function () {
            return new PluginManager();
        });
    }

    /**
     * Register the theme manager.
     */
    protected function registerThemeManager(): void
    {
        $this->app->singleton('theme.manager', function () {
            return new ThemeManager();
        });
    }

    /**
     * Register MediaForge service.
     */
    protected function registerMediaForge(): void
    {
        $this->app->singleton('media.forge', function () {
            return new MediaForgeService();
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'hook',
            'plugin.manager',
            'theme.manager',
            'media.forge',
        ];
    }
}
