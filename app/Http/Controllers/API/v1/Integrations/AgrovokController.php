<?php

namespace App\Http\Controllers\API\v1\Integrations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agrovok\ListAgrovokKeywordsRequest;
use App\Utilities\SCIO\TokenGenerator;
use Exception;
use Http;

class AgrovokController extends Controller
{
    public function callAgrovok(ListAgrovokKeywordsRequest $request)
    {
        $generator = new TokenGenerator();
        $url = env('SCIO_SERVICES_BASE_API_URL') . '/autocompleteagrovoc';

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
                'autocomplete' => $search,
                'language' => 'en'
            ]);

        // Unwrap the outer array.
        $json = $response->json()[0];

        return $json;
    }
}
