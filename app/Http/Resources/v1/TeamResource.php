<?php

namespace App\Http\Resources\v1;

use App\Enums\ResourceStatus;
use App\Models\Collection;
use App\Models\Resource;
use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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

        $resourceIds = DB::table('collection_resource')
            ->whereIn('collection_id', $collectionIds)
            ->pluck('resource_id');

        $resources = Resource::whereIn('status', [
            ResourceStatus::UNDER_PREPARATION,
            ResourceStatus::UNDER_REVIEW,
            ResourceStatus::APPROVED
        ])->whereIn('id', $resourceIds)->get();

        $activeTasks = collect($resources)->reduce(function ($sum, $task) {
            if ($task->status === ResourceStatus::UNDER_PREPARATION) {
                $sum++;
            }
            return $sum;
        }, 0);

        $pendingReviewTasks = collect($resources)->reduce(function ($sum, $task) {
            if ($task->status === ResourceStatus::UNDER_REVIEW) {
                $sum++;
            }
            return $sum;
        }, 0);

        $pendingUploadTasks = collect($resources)->reduce(function ($sum, $task) {
            if ($task->status === ResourceStatus::APPROVED) {
                $sum++;
            }
            return $sum;
        }, 0);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active_tasks' => $activeTasks,
            'pending_review_tasks' => $pendingReviewTasks,
            'pending_upload_tasks' => $pendingUploadTasks,
        ];
    }
}
