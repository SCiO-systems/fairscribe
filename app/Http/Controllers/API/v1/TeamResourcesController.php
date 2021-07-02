<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\ResourceStatus;
use App\Http\Requests\TeamResources\CreateTeamResourceRequest;
use App\Http\Requests\TeamResources\UpdateTeamResourceRequest;
use App\Http\Requests\TeamResources\GetSingleTeamResourceRequest;
use App\Http\Requests\TeamResources\ListTeamResourcesRequest;
use App\Models\Resource;
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SingleResourceResource;
use App\Http\Resources\v1\TeamResourceResource;
use Auth;
use DB;

class TeamResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListTeamResourcesRequest $request, Team $team)
    {
        $userId = Auth::user()->id;
        $resources = $team->resources();

        // TODO: Refactor this.
        if (!empty($request->status)) {
            $resourceIds = [];
            if ($request->status === ResourceStatus::UNDER_PREPARATION) {
                $resourceIds = DB::table('resource_authors')
                    ->where('user_id', $userId)
                    ->pluck('resource_id');
            }
            if ($request->status === ResourceStatus::UNDER_REVIEW) {
                $resourceIds = DB::table('resource_reviewers')
                    ->where('user_id', $userId)
                    ->pluck('resource_id');
            }
            $resources = $resources->where('status', $request->status);
            if (!empty($resourceIds)) {
                $resources = $resources->whereIn('id', $resourceIds);
            }
        }

        $resources = $resources->paginate();

        return TeamResourceResource::collection($resources);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeamResourceRequest $request, Team $team)
    {
        // Get the author.
        $authorId = Auth::user()->id;

        // Team leader is added in both the review as well as the authoring team.
        // The teams are filtered by checking if the members belong to the team.
        $authoringTeam = $team->users()->whereIn('user_id', $request->authoring_team)
            ->pluck('user_id');

        // Add the team leader and author in the authoring team.
        $authoringTeam->push($team->owner_id, $authorId);

        // The team leader should be in the review team as well.
        $reviewTeam = $team->users()->whereIn('user_id', $request->review_team)
            ->pluck('user_id');
        $reviewTeam->push($team->owner_id);

        // The collections that this resource will belong to.
        // The collections are filtered using the team collections.
        $collections = [];
        if (!empty($request->collections)) {
            $collections = $team->collections()->whereIn('id', $request->collections)->pluck('id');
        }

        // Create the resource with proper status.
        $resource = Resource::create([
            'title' => $request->title,
            'team_id' => $team->id,
            'description' => $request->description,
            'type' => $request->type,
            'status' => ResourceStatus::UNDER_PREPARATION,
            'author_id' => $authorId,
            'version' => 1
        ]);

        // Set the collections for a resource.
        if (!empty($request->collections)) {
            $resource->setCollections($collections);
        }

        // Set review team with team owner.
        $resource->setReviewTeam($reviewTeam);

        // Set the authoring team with team owner and author.
        $resource->setAuthoringTeam($authoringTeam);

        return new SingleResourceResource($resource);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GetSingleTeamResourceRequest $request, Team $team, Resource $resource)
    {
        return new SingleResourceResource($resource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeamResourceRequest $request, Team $team, Resource $resource)
    {
        // The collections that this resource will belong to.
        // The collections are filtered using the team collections.
        $collections = [];
        if (!empty($request->collections)) {
            $collections = $team->collections()->whereIn('id', $request->collections)->pluck('id');
        }

        $resource->setCollections($collections);

        if (!empty($request->status)) {
            $resource->status = $request->status;
        }

        if (!empty($request->metadata_record)) {
            $resource->setOrCreateMetadataRecord($request->metadata_record);
        }

        $resource->save();

        return new SingleResourceResource($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
