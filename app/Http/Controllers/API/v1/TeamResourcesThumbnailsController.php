<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamResourceThumbnails\CreateTeamResourceThumbnailRequest;
use App\Http\Requests\TeamResourceThumbnails\DeleteTeamResourceThumbnailRequest;
use App\Http\Requests\TeamResourceThumbnails\ListTeamResourceThumbnailsRequest;
use App\Http\Requests\TeamResourceThumbnails\ShowTeamResourceThumbnailRequest;
use App\Http\Resources\v1\TeamResourceThumbnailResource;
use App\Models\Resource;
use App\Models\ResourceThumbnail;
use App\Models\Team;
use Storage;

class TeamResourcesThumbnailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(
        ListTeamResourceThumbnailsRequest $request,
        Team $team,
        Resource $resource
    ) {
        $thumbnails = $resource->thumbnails()->paginate();

        return TeamResourceThumbnailResource::collection($thumbnails);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(
        CreateTeamResourceThumbnailRequest $request,
        Team $team,
        Resource $resource
    ) {
        $file = $request->file('file');
        $hash = $file->hashName();
        $directory = 'resource_thumbnails';
        $path = "$directory/$hash";
        $saved = $file->storeAs($directory, $hash);

        $resourceThumbnail = null;
        if ($saved) {
            $resourceThumbnail = ResourceThumbnail::create([
                'resource_id' => $resource->id,
                'original_filename' => $file->getClientOriginalName(),
                'path' => $path,
                'extension' => $file->extension(),
                'mimetype' => $file->getMimeType()
            ]);
        }

        return new TeamResourceThumbnailResource($resourceThumbnail);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(
        ShowTeamResourceThumbnailRequest $request,
        Team $team,
        Resource $resource,
        ResourceThumbnail $thumbnail
    ) {
        return new TeamResourceThumbnailResource($thumbnail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        DeleteTeamResourceThumbnailRequest $request,
        Team $team,
        Resource $resource,
        ResourceThumbnail $thumbnail
    ) {
        $fileDeleted = Storage::disk('local')->delete($thumbnail->path);

        if ($fileDeleted) {
            $dbEntryDeleted = $thumbnail->delete();
            if ($dbEntryDeleted) {
                return response()->json([], 204);
            }
        }

        return response()->json(['errors' => [
            'error' => 'Something went wrong'
        ]], 400);
    }
}
