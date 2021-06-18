<?php

namespace App\Http\Resources\v1;

use App\Models\Collection;
use App\Models\Resource;
use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $collectionIds = Collection::where('team_id', $this->id)
            ->pluck('id');

        $resources = DB::table('resources')
            ->whereIn('collection_id', $collectionIds)
            ->pluck('resource_id');

        $activeTasks = Resource::where('status', 'under_preparation')
            ->whereIn('id', $resources)
            ->count();

        $pendingReview = Resource::where('status', 'under_review')
            ->whereIn('id', $resources)
            ->count();

        $pendingUpload = Resource::where('status', 'approved')
            ->whereIn('id', $resources)
            ->count();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'activeTasks' => $activeTasks,
            'pendingReviewTasks' => $pendingReview,
            'pendingUploadTasks' => $pendingUpload,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
