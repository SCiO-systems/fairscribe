<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceFile extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'resource_files';

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
