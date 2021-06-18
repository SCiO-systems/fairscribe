<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamInvites\CreateTeamInviteRequest;
use App\Http\Resources\v1\InviteResource;
use App\Models\Invite;
use App\Models\Team;

class TeamInvitesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeamInviteRequest $request, Team $team)
    {
        // Add the emails to a collection.
        $emails = collect($request->emails);

        // The user attempted to invite themselves.
        if ($emails->contains($request->user()->email)) {
            return response()->json([
                'errors' => [
                    'error' => 'You cannot invite yourself to your own team.'
                ]
            ], 409);
        }

        // Check for already invited users.
        $invited = Invite::whereIn('email', $emails)
            ->where('team_id', $team->id)
            ->pluck('email');

        // Invite the non-invited emails.
        $emails = $emails->diff($invited)->each(function ($email) use ($team) {
            Invite::create(['email' => $email, 'team_id' => $team->id]);
        });

        // Gather the sent invites.
        $sentInvites = Invite::whereIn('email', $emails)->get();

        return InviteResource::collection($sentInvites);
    }
}
