<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\PIIStatus;
use App\Http\Requests\TeamResourceFiles\CreateTeamResourceFileRequest;
use App\Http\Requests\TeamResourceFiles\ListTeamResourceFilesRequest;
use App\Http\Requests\TeamResourceFiles\DeleteTeamResourceFileRequest;
use App\Http\Requests\TeamResourceFiles\ShowTeamResourceFileRequest;
use App\Http\Resources\v1\TeamResourceFileResource;
use App\Models\Resource;
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Models\ResourceFile;
use Illuminate\Support\Str;
use Storage;

class TeamResourcesFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListTeamResourceFilesRequest $request, Team $team, Resource $resource)
    {
        $files = $resource->files()->paginate();

        return TeamResourceFileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeamResourceFileRequest $request, Team $team, Resource $resource)
    {
        $resourceId = $resource->id;
        $file = $request->file('file');
        $name = Str::uuid();
        $directory = "resource_files/$resourceId";
        $saved = Storage::putFileAs($directory, $file, $name);

        $resourceFile = null;
        if ($saved) {
            $resourceFile = ResourceFile::create([
                'resource_id' => $resource->id,
                'filename' => $file->getClientOriginalName(),
                'path' => "$directory/$name",
                'pii_check' => PIIStatus::PENDING,
                'extension' => $file->extension(),
                'mimetype' => $file->getMimeType()
            ]);
        }

        // TODO: Dispatch job for PII check.

        return new TeamResourceFileResource($resourceFile);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(
        ShowTeamResourceFileRequest $request,
        Team $team,
        Resource $resource,
        ResourceFile $file
    ) {
        return new TeamResourceFileResource($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        DeleteTeamResourceFileRequest $request,
        Team $team,
        Resource $resource,
        ResourceFile $file
    ) {
        $fileDeleted = Storage::delete($file->path);

        if ($fileDeleted) {
            $dbEntryDeleted = $file->delete();
            if ($dbEntryDeleted) {
                return response()->json([], 204);
            }
        }

        return response()->json(['errors' => [
            'error' => 'Something went wrong'
        ]], 400);
    }
}
