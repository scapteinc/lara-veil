<?php

namespace Scapteinc\LaraVeil\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'thumbnail_path',
        'parent_id',
        'is_active',
        'settings',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'json',
        'metadata' => 'json',
    ];

    protected $table = 'themes';

    /**
     * Get parent theme
     */
    public function parent()
    {
        return $this->belongsTo(Theme::class, 'parent_id');
    }

    /**
     * Get child themes
     */
    public function children()
    {
        return $this->hasMany(Theme::class, 'parent_id');
    }

    /**
     * Check if theme is child of another theme
     */
    public function isChild(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }
        return asset('vendor/lara-veil/images/theme-placeholder.png');
    }
}

