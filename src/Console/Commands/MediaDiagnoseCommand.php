<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Media;
use Scapteinc\LaraVeil\Models\Plugin;
use Scapteinc\LaraVeil\Models\Theme;
use Illuminate\Support\Facades\Storage;

class MediaDiagnoseCommand extends Command
{
    protected $signature = 'media:diagnose';
    protected $description = 'Run diagnostics on media library';

    public function handle()
    {
        $this->info('=== Media Library Diagnostics ===');
        $this->newLine();

        $issues = [];

        // Check storage directory
        $this->line('Checking storage directory...');
        $storagePath = storage_path('app/public');
        if (!is_dir($storagePath)) {
            $issues[] = "Storage directory does not exist: {$storagePath}";
            $this->error('✗ Storage directory missing');
        } else {
            $this->line('<fg=green>✓</> Storage directory exists');
        }

        // Check disk configuration
        $this->line('Checking disk configuration...');
        try {
            Storage::disk('public')->exists('.');
            $this->line('<fg=green>✓</> Public disk is configured and accessible');
        } catch (\Exception $e) {
            $issues[] = "Public disk configuration error: " . $e->getMessage();
            $this->error('✗ Public disk configuration error: ' . $e->getMessage());
        }

        // Check database tables
        $this->newLine();
        $this->line('Checking database tables...');

        $tables = [
            'media' => Media::class,
            'plugins' => Plugin::class,
            'themes' => Theme::class,
        ];

        foreach ($tables as $table => $model) {
            try {
                $count = $model::count();
                $this->line("<fg=green>✓</> Table '{$table}' exists ({$count} records)");
            } catch (\Exception $e) {
                $issues[] = "Table '{$table}' missing or inaccessible";
                $this->error("✗ Table '{$table}' missing or inaccessible");
            }
        }

        // Check file integrity
        $this->newLine();
        $this->line('Checking file integrity...');

        $mediaFiles = Media::all();
        $missingCount = 0;
        $diskPath = Storage::disk('public');

        foreach ($mediaFiles as $media) {
            if (!$diskPath->exists($media->path)) {
                $missingCount++;
            }
        }

        if ($missingCount > 0) {
            $issues[] = "{$missingCount} media records reference missing files";
            $this->warn("⚠ {$missingCount} missing file(s) out of " . $mediaFiles->count() . " total");
        } else {
            $this->line("<fg=green>✓</> All {$mediaFiles->count()} media files accounted for");
        }

        // Check permissions
        $this->newLine();
        $this->line('Checking file permissions...');

        if (is_writable($storagePath)) {
            $this->line('<fg=green>✓</> Storage directory is writable');
        } else {
            $issues[] = "Storage directory is not writable";
            $this->error('✗ Storage directory is not writable');
        }

        // Summary
        $this->newLine();
        if (empty($issues)) {
            $this->info('✓ All diagnostics passed. Media library is healthy.');
            return 0;
        } else {
            $this->error('✗ Found ' . count($issues) . ' issue(s):');
            foreach ($issues as $issue) {
                $this->line("  • {$issue}");
            }
            return 1;
        }
    }
}
