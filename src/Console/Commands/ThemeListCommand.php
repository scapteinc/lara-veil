<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Theme;

class ThemeListCommand extends Command
{
    protected $signature = 'theme:list';
    protected $description = 'List all installed themes';

    public function handle()
    {
        $themes = Theme::all();

        if ($themes->isEmpty()) {
            $this->info('No themes installed.');
            return;
        }

        $this->table(
            ['Name', 'Slug', 'Version', 'Status', 'Updated At'],
            $themes->map(function ($theme) {
                return [
                    $theme->name,
                    $theme->slug,
                    $theme->version,
                    $theme->is_active ? '<fg=green>Active</>' : '<fg=red>Inactive</>',
                    $theme->updated_at->format('Y-m-d H:i'),
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info('Total: ' . count($themes) . ' theme(s)');
    }
}
