<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvites\UserInviteAcceptRequest;
use App\Http\Requests\UserInvites\UserInviteListRequest;
use App\Http\Requests\UserInvites\UserInviteRejectRequest;
use App\Http\Resources\v1\InviteResource;
use App\Models\Invite;
use App\Models\Team;
use App\Models\User;

class UserInvitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserInviteListRequest $request, User $user)
    {
        $pending = Invite::where('email', $request->user()->email)
            ->where('status', Invite::StatusPending)
            ->get();

        return InviteResource::collection($pending);
    }

    /**
     * Accept an invite.
     *
     * @return \Illuminate\Http\Response
     */
    public function accept(UserInviteAcceptRequest $request, User $user, Invite $invite)
    {
        $found = $invite->where('status', Invite::StatusPending)->first();

        if (!$found) {
            return response()->json(['errors' => [
                'error' => 'The invite was not found.'
            ]], 404);
        }

        $invite->update(['status' => Invite::StatusAccepted]);

        // Check if the team exists.
        $team = Team::find($invite->team_id)->first();

        if (!$team) {
            return response()->json(['errors' => [
                'error' => 'The team for this invite was not found.'
            ]], 404);
        }

        // Add the user to the team.
        $team->users()->attach($user);

        return new InviteResource($invite);
    }


    /**
     * Reject an invite.
     *
     * @return \Illuminate\Http\Response
     */
    public function reject(UserInviteRejectRequest $request, User $user, Invite $invite)
    {
        $found = $invite->where('status', Invite::StatusPending)->first();

        if (!$found) {
            return response()->json(['errors' => ['error' => 'The invite was not found.']], 404);
        }

        $invite->update(['status' => Invite::StatusRejected]);

        return new InviteResource($invite);
    }
}
