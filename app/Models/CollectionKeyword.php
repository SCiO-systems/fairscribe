<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionKeyword extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'collection_keywords';

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
