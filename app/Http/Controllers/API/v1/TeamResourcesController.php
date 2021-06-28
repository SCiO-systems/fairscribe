<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\ResourceStatus;
use App\Http\Requests\TeamResources\GetSingleTeamResourceRequest;
use App\Http\Requests\TeamResources\ListTeamResourcesRequest;
use App\Models\Resource;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamResources\CreateTeamResourceRequest;
use App\Http\Resources\v1\SingleResourceResource;
use App\Http\Resources\v1\TeamResourceResource;
use App\Models\Collection;
use Auth;

class TeamResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListTeamResourcesRequest $request, Team $team)
    {
        $resources = $team->resources();

        if (!empty($request->status)) {
            $resources = $resources->where('status', $request->status);
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
        $authoringTeam->push($team->owner_id);
        $authoringTeam->push($authorId);

        // The team leader should be in the review team as well.
        $reviewTeam = $team->users()->whereIn('user_id', $request->review_team)
            ->pluck('user_id');
        $reviewTeam->push($team->owner_id);

        // The collections that this resource will belong to.
        // The collections are filtered using the team collections.
        $collections = $team->collections()->whereIn('id', $request->collections)->pluck('id');

        // Create the resource with proper status.
        $resource = Resource::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'status' => ResourceStatus::UNDER_PREPARATION,
            'author_id' => $authorId,
            'version' => 1
        ]);

        // Set the collections for a resource.
        $resource->setCollections($collections);

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
    public function update(Request $request, $id)
    {
        //
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
