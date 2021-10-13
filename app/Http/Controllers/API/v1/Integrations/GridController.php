<?php

namespace App\Http\Controllers\API\v1\Integrations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grid\ListGridItemsRequest;
use App\Utilities\SCIO\TokenGenerator;
use Exception;
use Http;

class GridController extends Controller
{
    public function callGrid(ListGridItemsRequest $request)
    {
        $generator = new TokenGenerator();
        $url = env('SCIO_SERVICES_BASE_API_URL') . '/autocompletegrid';

        try {
            $token = $generator->getToken();
        } catch (Exception $ex) {
            throw $ex;
        }

        $search = $request->search;

        $response = Http::timeout(5)
            ->acceptJson()
            ->asJson()
            ->withToken($token)
            ->asForm()
            ->get($url, [
                'data' => $search,
            ]);

        // Unwrap the outer array.
        $json = $response->json()["data"];

        return $json;
    }
}
