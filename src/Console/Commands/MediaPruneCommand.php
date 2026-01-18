<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaPruneCommand extends Command
{
    protected $signature = 'media:prune {--days=30 : Delete media older than N days} {--dry-run : Preview changes without deleting}';
    protected $description = 'Delete old media files';

    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subDays($days);

        $this->info($dryRun ? "DRY RUN: Preview media files older than {$days} days" : "Deleting media files older than {$days} days...");

        $deleted = 0;

        // Get media files older than cutoff date
        $oldMedia = Media::where('created_at', '<', $cutoffDate)->get();

        if ($oldMedia->isEmpty()) {
            $this->info('No media files to prune.');
            return 0;
        }

        $disk = Storage::disk('public');

        foreach ($oldMedia as $media) {
            $this->line("Pruning: {$media->path} (created " . $media->created_at->format('Y-m-d H:i') . ")");

            if (!$dryRun) {
                try {
                    if ($disk->exists($media->path)) {
                        $disk->delete($media->path);
                    }
                    $media->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $this->error("Failed to delete {$media->path}: " . $e->getMessage());
                }
            } else {
                $deleted++;
            }
        }

        $this->newLine();
        $this->info("Prune complete. Removed: {$deleted} file(s)");

        if ($dryRun) {
            $this->warn('This was a dry run. Use without --dry-run to apply changes.');
        }

        return 0;
    }
}
