<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Plugin;

class PluginInstallCommand extends Command
{
    protected $signature = 'plugin:install {path : Path to plugin directory or composer package}';
    protected $description = 'Install a new plugin';

    public function handle()
    {
        $path = $this->argument('path');

        // Check if plugin directory exists
        if (!is_dir($path) && !$this->isComposerPackage($path)) {
            $this->error("Plugin path '{$path}' does not exist.");
            return 1;
        }

        try {
            // If it's a composer package, install via composer
            if ($this->isComposerPackage($path)) {
                $this->info("Installing plugin via Composer: {$path}");
                exec("composer require {$path}", $output, $returnCode);
                if ($returnCode !== 0) {
                    $this->error("Failed to install plugin via Composer.");
                    return 1;
                }
            }

            // Parse plugin metadata
            $configFile = $path . '/config/plugin.php';
            if (!file_exists($configFile)) {
                $this->error("Plugin configuration file not found: {$configFile}");
                return 1;
            }

            $pluginConfig = require $configFile;

            // Create plugin record
            $plugin = Plugin::updateOrCreate(
                ['namespace' => $pluginConfig['namespace']],
                [
                    'name' => $pluginConfig['name'],
                    'version' => $pluginConfig['version'] ?? '1.0.0',
                    'is_active' => false,
                ]
            );

            app('hook')->doAction('plugin.installed', $plugin);

            $this->info("Plugin '{$pluginConfig['name']}' installed successfully.");
            $this->line("Run <fg=yellow>php artisan plugin:activate {$pluginConfig['namespace']}</> to activate it.");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error installing plugin: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if path is a composer package name
     */
    protected function isComposerPackage(string $path): bool
    {
        return str_contains($path, '/') && !str_starts_with($path, '/');
    }
}
