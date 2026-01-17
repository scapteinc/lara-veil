<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Plugin;

class PluginDeactivateCommand extends Command
{
    protected $signature = 'plugin:deactivate {plugin : The plugin namespace}';
    protected $description = 'Deactivate a plugin';

    public function handle()
    {
        $namespace = $this->argument('plugin');
        $plugin = Plugin::where('namespace', $namespace)->first();

        if (!$plugin) {
            $this->error("Plugin '{$namespace}' not found.");
            return 1;
        }

        if (!$plugin->is_active) {
            $this->warn("Plugin '{$namespace}' is already inactive.");
            return 0;
        }

        $plugin->update(['is_active' => false]);
        app('hook')->doAction('plugin.deactivated', $plugin);

        $this->info("Plugin '{$namespace}' deactivated successfully.");
        return 0;
    }
}
