<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Theme;

class ThemeActivateCommand extends Command
{
    protected $signature = 'theme:activate {theme : The theme slug}';
    protected $description = 'Activate a theme';

    public function handle()
    {
        $slug = $this->argument('theme');
        $theme = Theme::where('slug', $slug)->first();

        if (!$theme) {
            $this->error("Theme '{$slug}' not found.");
            return 1;
        }

        try {
            // Deactivate all other themes
            Theme::where('id', '!=', $theme->id)->update(['is_active' => false]);

            // Activate selected theme
            $theme->update(['is_active' => true]);

            app('theme.manager')->setActive($slug);
            app('hook')->doAction('theme.activated', $theme);

            $this->info("Theme '{$slug}' activated successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error activating theme: " . $e->getMessage());
            return 1;
        }
    }
}
