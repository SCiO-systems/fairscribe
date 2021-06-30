<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceThumbnail extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'resource_thumbnails';

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
