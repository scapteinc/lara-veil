<?php

namespace Scapteinc\LaraVeil\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'name',
        'path',
        'media_type',
        'mime_type',
        'file_size',
        'width',
        'height',
        'disk',
        'model_type',
        'model_id',
        'collection_name',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    protected $table = 'media';

    /**
     * Get the owning model (polymorphic relationship)
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the public URL for this media
     */
    public function getPublicUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Check if this is an image
     */
    public function isImage(): bool
    {
        return $this->media_type === 'image' || str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
