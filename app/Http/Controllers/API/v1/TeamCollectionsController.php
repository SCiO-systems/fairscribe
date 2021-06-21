<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamCollections\ListSingleTeamCollectionRequest;
use App\Http\Requests\TeamCollections\ListTeamCollectionsRequest;
use App\Http\Resources\v1\TeamCollectionResource;
use App\Models\Collection;
use App\Models\Team;

class TeamCollectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListTeamCollectionsRequest $request, Team $team)
    {
        $collections = Collection::where('team_id', $team->id)->paginate();

        return TeamCollectionResource::collection($collections);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(
        ListSingleTeamCollectionRequest $request,
        Team $team,
        Collection $collection
    ) {
        return new TeamCollectionResource($collection);
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
