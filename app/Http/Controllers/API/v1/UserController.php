<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\ListUsersRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\ShowUserRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of all the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListUsersRequest $request)
    {
        $users = User::where('firstname', 'like', '%' . $request->name . '%')
            ->orWhere('lastname', 'like', '%' . $request->name . '%')
            ->orWhere('email', 'like', '%' . $request->name . '%')
            ->get();

        return UserResource::collection($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShowUserRequest $request, User $user)
    {
        return new UserResource($request->user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Filter null and falsy values.
        // TODO: Check for SQLi.
        $data = collect($request->except(['avatar_url']))->filter()->all();

        // Update the user details with the new ones.
        $request->user()->update($data);

        return new UserResource($request->user());
    }
}
