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
        try {
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
        } catch (\Exception $e) {
            // Silently fail during uninstall
        }
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

        // Publish views and components
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/lara-veil'),
        ], 'lara-veil-views');

        // Publish precompiled assets to public directory
        $this->publishes([
            __DIR__ . '/../../resources/css' => public_path('vendor/lara-veil/css'),
            __DIR__ . '/../../resources/js' => public_path('vendor/lara-veil/js'),
        ], 'lara-veil-assets');

        // Publish all resources
        $this->publishes([
            __DIR__ . '/../../resources' => resource_path('vendor/lara-veil'),
        ], 'lara-veil-all');

        // Load migrations from package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load views from package
        // $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'lara-veil');

        // // Load routes from package (only in non-console environment)
        // if (!$this->app->runningInConsole()) {
        //     // $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        //     // $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        //     // Register Livewire components from package
        //     try {
        //         if (class_exists('\Livewire\Livewire')) {
        //             \Livewire\Livewire::component('lara-veil.dashboard', \Scapteinc\LaraVeil\Http\Livewire\Dashboard::class);
        //             \Livewire\Livewire::component('lara-veil.media-gallery-grid', \Scapteinc\LaraVeil\Http\Livewire\MediaGalleryGrid::class);
        //             \Livewire\Livewire::component('lara-veil.media-gallery', \Scapteinc\LaraVeil\Http\Livewire\MediaGallery::class);
        //             \Livewire\Livewire::component('lara-veil.media-uploader', \Scapteinc\LaraVeil\Http\Livewire\MediaUploader::class);
        //             \Livewire\Livewire::component('lara-veil.media-editor', \Scapteinc\LaraVeil\Http\Livewire\MediaEditor::class);
        //             \Livewire\Livewire::component('lara-veil.plugins-gallery', \Scapteinc\LaraVeil\Http\Livewire\PluginsGallery::class);
        //             \Livewire\Livewire::component('lara-veil.plugins-table', \Scapteinc\LaraVeil\Http\Livewire\PluginsTable::class);
        //             \Livewire\Livewire::component('lara-veil.themes-gallery', \Scapteinc\LaraVeil\Http\Livewire\ThemesGallery::class);
        //             \Livewire\Livewire::component('lara-veil.themes-table', \Scapteinc\LaraVeil\Http\Livewire\ThemesTable::class);
        //             \Livewire\Livewire::component('lara-veil.settings-form', \Scapteinc\LaraVeil\Http\Livewire\SettingsForm::class);
        //         }
        //     } catch (\Exception $e) {
        //         // Livewire not available, skip
        //     }
        // }

        // Register console commands
        if ($this->app->runningInConsole()) {
            try {
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
            } catch (\Exception $e) {
                // Silently fail if commands can't be registered during uninstall
            }
        }

        // Execute system init hook (only if not in console)
        if (!$this->app->runningInConsole() && $this->app->resolved('hook')) {
            $this->app['hook']->doAction('system.init');
        }

        // Auto-discover and sync plugins and themes from filesystem
        $this->syncExtensions();
    }

    /**
     * Auto-discover and sync plugins and themes from filesystem.
     */
    protected function syncExtensions(): void
    {
        // Only sync in non-console, non-testing environments to avoid overhead
        if ($this->app->runningInConsole() || $this->app->environment('testing')) {
            return;
        }

        try {
            // Check if database tables exist before syncing
            if (!\Illuminate\Support\Facades\Schema::hasTable('plugins')) {
                return;
            }

            $pluginManager = $this->app->make('plugin.manager');
            $themeManager = $this->app->make('theme.manager');

            // Sync plugins and themes from filesystem
            $pluginManager->syncPlugins();
            $themeManager->syncThemes();
        } catch (\Throwable $e) {
            // Silently fail to avoid breaking the application during bootstrap
            \Illuminate\Support\Facades\Log::debug('Extension sync failed: ' . $e->getMessage());
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
