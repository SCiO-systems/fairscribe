<?php

namespace App\Http\Controllers\API\v1\Integrations;

use App\Http\Controllers\Controller;
use App\Http\Requests\SCiO\Agrovok\ListAgrovokKeywordsRequest;
use App\Http\Requests\SCiO\Grid\ListGridItemsRequest;
use App\Http\Requests\SCiO\Languages\ListLanguagesRequest;
use App\Http\Requests\SCiO\Mimetypes\GetMimetypeRequest;
use App\Http\Requests\SCiO\Vocabularies\ListVocabulariesRequest;
use App\Utilities\SCIO\TokenGenerator;
use Cache;
use Exception;
use Http;
use Illuminate\Http\Request;

class ScioController extends Controller
{

    protected $token;
    protected $cacheTtl;
    protected $baseURI;

    public function __construct()
    {
        $this->token = '';
        $this->cacheTtl = env('CACHE_TTL_SECONDS');
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL');
    }

    public function listLanguages(ListLanguagesRequest $request)
    {
        $cacheKey = 'scio_languages';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $generator = new TokenGenerator();
            $this->token = $generator->getToken();
        } catch (Exception $ex) {
            throw $ex;
        }

        $response = Http::timeout(env('REQUEST_TIMEOUT_SECONDS'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->token)
            ->get("$this->baseURI/languages/languagelist");

        $json = $response->json('languages');

        if ($response->ok()) {
            Cache::put($cacheKey, $json, $this->cacheTtl);
        }

        return response()->json($json, $response->status());
    }

    public function getMimetype(GetMimetypeRequest $request)
    {
        try {
            $generator = new TokenGenerator();
            $this->token = $generator->getToken();
        } catch (Exception $ex) {
            throw $ex;
        }

        $response = Http::timeout(env('REQUEST_TIMEOUT_SECONDS'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->token)
            ->post("$this->baseURI/vocabularies/resolvemimetypes", $request->all());

        $json = $response->json('response');
        $statusCode = $response->json('code');

        return response()->json($json, $statusCode);
    }

    public function listVocabularies(ListVocabulariesRequest $request)
    {
        $cacheKey = 'scio_vocabularies';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $generator = new TokenGenerator();
            $this->token = $generator->getToken();
        } catch (Exception $ex) {
            throw $ex;
        }

        $response = Http::timeout(env('REQUEST_TIMEOUT_SECONDS'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->token)
            ->post("$this->baseURI/vocabularies/getvocabularies", [
                'language' => 'eng',
                'types_enabled' => ['keyword', 'extracted']
            ]);

        $json = $response->json('response');

        if ($response->ok()) {
            Cache::put($cacheKey, $json, $this->cacheTtl);
        }

        return response()->json($json, $response->status());
    }
}
