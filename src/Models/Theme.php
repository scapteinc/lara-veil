<?php

namespace Scapteinc\LaraVeil\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'json',
    ];

    protected $table = 'themes';

    public function parent()
    {
        return $this->belongsTo(Theme::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Theme::class, 'parent_id');
    }
}
