<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Plugin;

class PluginListCommand extends Command
{
    protected $signature = 'plugin:list';
    protected $description = 'List all installed plugins';

    public function handle()
    {
        $plugins = Plugin::all();

        if ($plugins->isEmpty()) {
            $this->info('No plugins installed.');
            return;
        }

        $this->table(
            ['Name', 'Namespace', 'Version', 'Status', 'Updated At'],
            $plugins->map(function ($plugin) {
                return [
                    $plugin->name,
                    $plugin->namespace,
                    $plugin->version,
                    $plugin->is_active ? '<fg=green>Active</>' : '<fg=red>Inactive</>',
                    $plugin->updated_at->format('Y-m-d H:i'),
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info('Total: ' . count($plugins) . ' plugin(s)');
    }
}
