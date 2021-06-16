<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetedRepository extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'user_repositories';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
