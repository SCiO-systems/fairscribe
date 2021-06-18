<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserTeamInvites\UserTeamInviteCreateRequest;
use App\Mail\TeamInviteSent;
use App\Models\Invite;
use App\Models\Team;
use App\Models\User;
use Mail;

class UserTeamInvitesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserTeamInviteCreateRequest $request, User $user, Team $team)
    {
        // Check if an invite already exists.
        $invite = Invite::where('email', $request->email)
            ->where('team_id', $team->id)
            ->first();

        if ($invite) {
            return response()->json([
                'errors' => [
                    'error' => 'An invite for the same email and team already exists.'
                ]
            ], 409);
        }

        // Create the invite.
        $invite = Invite::create([
            'email' => $request->email,
            'team_id' => $team->id,
            'status' => Invite::StatusPending
        ]);

        // Send the email.
        Mail::to($request->email)->queue(new TeamInviteSent($request->user(), $team));
    }
}
