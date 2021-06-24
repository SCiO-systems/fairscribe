<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\ResourceStatus;
use App\Http\Requests\UserStats\ListUserStatsRequest;
use App\Models\Resource;
use App\Http\Controllers\Controller;
use App\Models\User;
use DB;

class UserStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListUserStatsRequest $request, User $user)
    {
        $sharedTeamCollectionIds = $user->sharedTeams()
            ->join('collections', 'collections.team_id', 'teams.id')
            ->select('collections.id')
            ->pluck('id')
            ->toArray();

        $ownedTeamCollectionIds = $user->ownedTeams()
            ->join('collections', 'collections.team_id', 'teams.id')
            ->select('collections.id')
            ->pluck('id')
            ->toArray();

        // TODO: Refactor.
        $resourceIds = DB::table('collection_resource')
            ->whereIn('collection_id', array_merge(
                $sharedTeamCollectionIds,
                $ownedTeamCollectionIds
            ))->pluck('resource_id');

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

        return response()->json([
            'data' => [
                'active_tasks' => $activeTasks,
                'pending_review_tasks' => $pendingReviewTasks,
                'pending_upload_tasks' => $pendingUploadTasks,
            ]
        ]);
    }
}
