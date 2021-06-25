<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\TeamResources\GetSingleTeamResourceRequest;
use App\Http\Requests\TeamResources\ListTeamResourcesRequest;
use App\Models\Resource;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SingleResourceResource;
use App\Http\Resources\v1\TeamResourceResource;

class TeamResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListTeamResourcesRequest $request, Team $team)
    {
        $resources = $team->resources()->paginate();

        return TeamResourceResource::collection($resources);
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
