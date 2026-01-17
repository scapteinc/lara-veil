<?php

namespace Scapteinc\LaraVeil\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'name',
        'namespace',
        'version',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'json',
    ];

    protected $table = 'plugins';
}
