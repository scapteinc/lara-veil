<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaCleanupCommand extends Command
{
    protected $signature = 'media:cleanup {--orphaned : Remove only orphaned media files} {--dry-run : Preview changes without deleting}';
    protected $description = 'Clean up unreferenced media files';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $orphanedOnly = $this->option('orphaned');

        $this->info($dryRun ? 'DRY RUN: No files will be deleted.' : 'Starting media cleanup...');

        $deleted = 0;
        $failed = 0;

        // Get all media files from database
        $mediaFiles = Media::all();

        foreach ($mediaFiles as $media) {
            // Check if file exists in storage
            $disk = Storage::disk('public');

            if (!$disk->exists($media->path)) {
                $this->line("Missing: {$media->path}");

                if (!$dryRun) {
                    $media->delete();
                }
                $deleted++;
            } elseif ($orphanedOnly && !$media->mediaable_id) {
                // Delete orphaned media (not associated with any model)
                $this->line("Orphaned: {$media->path}");

                if (!$dryRun) {
                    $disk->delete($media->path);
                    $media->delete();
                }
                $deleted++;
            }
        }

        $this->newLine();
        $this->info("Cleanup complete. Removed: {$deleted} file(s)");

        if ($dryRun) {
            $this->warn('This was a dry run. Use without --dry-run to apply changes.');
        }

        return 0;
    }
}
