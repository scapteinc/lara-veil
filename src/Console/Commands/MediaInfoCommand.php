<?php

namespace Scapteinc\LaraVeil\Console\Commands;

use Illuminate\Console\Command;
use Scapteinc\LaraVeil\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaInfoCommand extends Command
{
    protected $signature = 'media:info';
    protected $description = 'Display media library statistics';

    public function handle()
    {
        $disk = Storage::disk('public');

        // Get statistics
        $totalMedia = Media::count();
        $totalSize = Media::sum('file_size');
        $imageCount = Media::where('media_type', 'image')->count();
        $videoCount = Media::where('media_type', 'video')->count();
        $fileCount = Media::where('media_type', 'file')->count();
        $orphanedCount = Media::whereNull('mediaable_id')->count();

        $this->info('=== Media Library Statistics ===');
        $this->newLine();

        // Display in table format
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Media Files', $totalMedia],
                ['Total Size', $this->formatBytes($totalSize ?? 0)],
                ['Images', $imageCount],
                ['Videos', $videoCount],
                ['Documents', $fileCount],
                ['Orphaned Files', $orphanedCount],
            ]
        );

        // Storage path info
        $this->newLine();
        $this->info('Storage Information:');
        $this->line('Disk: public');
        $this->line('Path: ' . storage_path('app/public'));

        // Recent uploads
        $this->newLine();
        $this->info('Recent Uploads (Last 5):');

        $recent = Media::latest()->limit(5)->get();

        if ($recent->isEmpty()) {
            $this->line('No media files uploaded yet.');
        } else {
            $this->table(
                ['Name', 'Type', 'Size', 'Uploaded At'],
                $recent->map(function ($media) {
                    return [
                        substr($media->name, 0, 30),
                        ucfirst($media->media_type),
                        $this->formatBytes($media->file_size),
                        $media->created_at->format('Y-m-d H:i'),
                    ];
                })->toArray()
            );
        }

        return 0;
    }

    /**
     * Format bytes to human-readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
