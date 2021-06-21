<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\ListAllTeamsRequest;
use App\Http\Requests\Teams\ListTeamsRequest;
use App\Http\Requests\Teams\ShowSingleTeamRequest;
use App\Http\Resources\v1\TeamResource;
use App\Http\Resources\v1\TeamResourceWithUsers;
use App\Models\Team;
use DB;

class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListTeamsRequest $request)
    {
        $books = DB::connection('mongodb')->table('books')->get();

        dd($books);


        $sharedTeams = $request->user()->teams()->paginate();

        return TeamResource::collection($sharedTeams);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowSingleTeamRequest $request, Team $team)
    {
        return new TeamResourceWithUsers($team);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(ListAllTeamsRequest $request)
    {
        $sharedTeams = $request->user()->teams()->get();

        return TeamResource::collection($sharedTeams);
    }
}
