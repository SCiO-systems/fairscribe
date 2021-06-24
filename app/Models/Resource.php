<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'resources';

    public function collections()
    {
        return $this->belongsToMany(
            Collection::class,
            'collection_resource',
            'resource_id',
            'collection_id'
        );
    }

    public function teams()
    {
        $teamIds = $this->collections()->pluck('collections.team_id');
        return Team::whereIn('id', $teamIds);
    }

    public function approve()
    {
        $this->status = 'approved';
        $this->save();
    }

    public function publish()
    {
        $this->status = 'published';
        $this->published_at = Carbon::now();
        $this->publisher_id = Auth::user()->id;
        $this->save();
    }
}
