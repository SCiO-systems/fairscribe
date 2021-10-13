<?php

namespace App\Http\Controllers\API\v1\Integrations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Languages\ListLanguagesRequest;
use App\Utilities\SCIO\TokenGenerator;
use Cache;
use Exception;
use Http;

class LanguagesController extends Controller
{
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

        Cache::put('scio_languages', $json, $ttl);

        return $json;
    }
}
