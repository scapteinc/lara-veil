<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Theme;

class ThemeInstallCommand extends Command
{
    protected $signature = 'theme:install {path : Path to theme directory or composer package}';
    protected $description = 'Install a new theme';

    public function handle()
    {
        $path = $this->argument('path');

        // Check if theme directory exists
        if (!is_dir($path) && !$this->isComposerPackage($path)) {
            $this->error("Theme path '{$path}' does not exist.");
            return 1;
        }

        try {
            // If it's a composer package, install via composer
            if ($this->isComposerPackage($path)) {
                $this->info("Installing theme via Composer: {$path}");
                exec("composer require {$path}", $output, $returnCode);
                if ($returnCode !== 0) {
                    $this->error("Failed to install theme via Composer.");
                    return 1;
                }
            }

            // Parse theme metadata
            $configFile = $path . '/config/theme.php';
            if (!file_exists($configFile)) {
                $this->error("Theme configuration file not found: {$configFile}");
                return 1;
            }

            $themeConfig = require $configFile;

            // Create theme record
            $theme = Theme::updateOrCreate(
                ['slug' => $themeConfig['slug']],
                [
                    'name' => $themeConfig['name'],
                    'version' => $themeConfig['version'] ?? '1.0.0',
                    'parent_id' => null,
                    'is_active' => false,
                ]
            );

            app('hook')->doAction('theme.installed', $theme);

            $this->info("Theme '{$themeConfig['name']}' installed successfully.");
            $this->line("Run <fg=yellow>php artisan theme:activate {$themeConfig['slug']}</> to activate it.");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error installing theme: " . $e->getMessage());
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
