<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Plugin;

class PluginActivateCommand extends Command
{
    protected $signature = 'plugin:activate {plugin : The plugin namespace}';
    protected $description = 'Activate a plugin';

    public function handle()
    {
        $namespace = $this->argument('plugin');
        $plugin = Plugin::where('namespace', $namespace)->first();

        if (!$plugin) {
            $this->error("Plugin '{$namespace}' not found.");
            return 1;
        }

        if ($plugin->is_active) {
            $this->warn("Plugin '{$namespace}' is already active.");
            return 0;
        }

        $plugin->update(['is_active' => true]);
        app('hook')->doAction('plugin.activated', $plugin);

        $this->info("Plugin '{$namespace}' activated successfully.");
        return 0;
    }
}
