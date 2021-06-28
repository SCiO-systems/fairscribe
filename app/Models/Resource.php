<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use Auth;
use Carbon\Carbon;
use DB;
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

    public function setCollections($collectionIds)
    {
        // TODO: Optimize.
        $collectionIds = collect($collectionIds)->unique()->all();
        foreach ($collectionIds as $id) {
            Collection::find($id)->resources()->attach($this->id);
        }
    }

    public function setReviewTeam($userIds)
    {
        // TODO: Change this to eloquent.
        $userIds = collect($userIds)->unique()->all();
        DB::table('resource_reviewers')->where('resource_id', $this->id)->delete();
        foreach ($userIds as $id) {
            DB::table('resource_reviewers')->insert([
                'resource_id' => $this->id,
                'user_id' => $id
            ]);
        }
    }

    public function setAuthoringTeam($userIds)
    {
        // TODO: Change this to eloquent.
        $userIds = collect($userIds)->unique()->all();
        DB::table('resource_authors')->where('resource_id', $this->id)->delete();
        foreach ($userIds as $id) {
            DB::table('resource_authors')->insert([
                'resource_id' => $this->id,
                'user_id' => $id
            ]);
        }
    }
}
