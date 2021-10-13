<?php

namespace App\Http\Controllers\API\v1\Integrations;

use App\Http\Controllers\Controller;
use App\Http\Requests\SCiO\Agrovok\ListAgrovokKeywordsRequest;
use App\Http\Requests\SCiO\Grid\ListGridItemsRequest;
use App\Http\Requests\SCiO\Languages\ListLanguagesRequest;
use App\Utilities\SCIO\TokenGenerator;
use Cache;
use Exception;
use Http;

class ScioController extends Controller
{
    public function agrovokAutocomplete(ListAgrovokKeywordsRequest $request)
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

    public function gridAutocomplete(ListGridItemsRequest $request)
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

    public function getLanguages(ListLanguagesRequest $request)
    {
        $generator = new TokenGenerator();
        $cacheKey = 'scio_languages';
        $url = env('SCIO_SERVICES_BASE_API_URL') . '/languages/languagelist';
        $ttl = env('CACHE_TTL_SECONDS');

        try {
            $token = $generator->getToken();
        } catch (Exception $ex) {
            throw $ex;
        }

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(5)
            ->acceptJson()
            ->asJson()
            ->withToken($token)
            ->get($url);

        $json = $response->json('languages');

        Cache::put($cacheKey, $json, $ttl);

        return $json;
    }
}
