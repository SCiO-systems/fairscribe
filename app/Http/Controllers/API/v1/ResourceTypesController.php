<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\ResourceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceTypes\ListResourceTypesRequest;

class ResourceTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListResourceTypesRequest $request)
    {
        $types = collect(ResourceType::asArray())->map(function ($value, $key) {
            return ["name" => $key, "value" => $value];
        })->values();

        return response()->json(['data' => $types], 200);
    }
}
