<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionGeospatialCoverage extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'collection_geospatial_coverages';

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
