<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'collections';

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function resources()
    {
        return $this->belongsToMany(
            Resource::class,
            'collection_resources',
            'resource_id',
            'collection_id'
        );
    }
}
