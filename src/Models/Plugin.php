<?php

namespace Scapteinc\LaraVeil\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'name',
        'namespace',
        'description',
        'version',
        'author',
        'status',
        'settings',
        'metadata',
    ];

    protected $casts = [
        'settings' => 'json',
        'metadata' => 'json',
    ];

    protected $table = 'plugins';

    /**
     * Check if plugin is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if plugin is broken
     */
    public function isBroken(): bool
    {
        return $this->status === 'broken';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'broken' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }
}

