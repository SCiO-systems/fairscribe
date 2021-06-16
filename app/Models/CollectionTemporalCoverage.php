<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionTemporalCoverage extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'collection_temporal_coverages';

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
