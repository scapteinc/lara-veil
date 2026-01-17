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

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/vormia.php',
            'vormia'
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
        // Publish configuration (optional - for customization)
        $this->publishes([
            __DIR__ . '/../../config/lara-veil.php' => config_path('lara-veil.php'),
            __DIR__ . '/../../config/vormia.php' => config_path('vormia.php'),
        ], 'lara-veil-config');

        // Publish migrations (optional - for customization)
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'lara-veil-migrations');

        // Publish assets (optional - for customization)
        $this->publishes([
            __DIR__ . '/../../resources' => resource_path('vendor/lara-veil'),
        ], 'lara-veil-assets');

        // Load Volt components from package
        \Laravel\Volt\Volt::useNamespace('lara-veil');

        // Load migrations from package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load views from package
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'lara-veil');

        // Load routes from package
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Plugin commands
                \Scapteinc\LaraVeil\Console\Commands\PluginListCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\PluginActivateCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\PluginDeactivateCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\PluginInstallCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\PluginUninstallCommand::class,

                // Theme commands
                \Scapteinc\LaraVeil\Console\Commands\ThemeListCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\ThemeActivateCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\ThemeInstallCommand::class,

                // Media commands
                \Scapteinc\LaraVeil\Console\Commands\MediaCleanupCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\MediaPruneCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\MediaInfoCommand::class,
                \Scapteinc\LaraVeil\Console\Commands\MediaDiagnoseCommand::class,
            ]);
        }

        // Execute system init hook
        if ($this->app->resolved('hook')) {
            $this->app['hook']->doAction('system.init');
        }
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
