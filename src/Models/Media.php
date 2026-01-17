<?php

namespace Scapteinc\LaraVeil\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'collection_name',
        'file_path',
        'disk',
        'mime_type',
        'size',
        'width',
        'height',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    protected $table = 'media';

    public function model()
    {
        return $this->morphTo();
    }
}
