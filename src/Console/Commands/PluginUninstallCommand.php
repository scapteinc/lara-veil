<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Plugin;

class PluginUninstallCommand extends Command
{
    protected $signature = 'plugin:uninstall {plugin : The plugin namespace} {--force : Force uninstall without confirmation}';
    protected $description = 'Uninstall a plugin';

    public function handle()
    {
        $namespace = $this->argument('plugin');
        $plugin = Plugin::where('namespace', $namespace)->first();

        if (!$plugin) {
            $this->error("Plugin '{$namespace}' not found.");
            return 1;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to uninstall '{$plugin->name}'?")) {
                $this->info('Uninstall cancelled.');
                return 0;
            }
        }

        try {
            app('hook')->doAction('plugin.uninstalling', $plugin);

            $plugin->delete();

            app('hook')->doAction('plugin.uninstalled', $namespace);

            $this->info("Plugin '{$namespace}' uninstalled successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error uninstalling plugin: " . $e->getMessage());
            return 1;
        }
    }
}
