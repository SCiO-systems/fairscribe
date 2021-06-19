<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RepositoryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\RepositoryTypes\ListRepositoryTypesRequest;
use App\Http\Resources\v1\RepositoryTypeResource;

class RepositoryTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListRepositoryTypesRequest $request)
    {
        $types = collect(RepositoryType::asArray())->map(function ($value) {
            return ['name' => $value, 'value' => $value];
        })->values()->all();

        return response()->json(['data' => $types], 200);
    }
}
