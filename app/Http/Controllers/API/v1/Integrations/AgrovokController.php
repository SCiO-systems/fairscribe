<?php

namespace App\Http\Controllers\API\v1\Integrations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agrovok\ListAgrovokKeywordsRequest;
use Cache;
use Http;

class AgrovokController extends Controller
{

    protected $baseURI;
    protected $clientID;
    protected $clientSecret;

    public function __construct()
    {
        $this->clientID = env('SCIO_SERVICES_CLIENT_ID');
        $this->clientSecret = env('SCIO_SERVICES_SECRET');
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL');
    }

    public function getToken()
    {
        $cachedToken = Cache::get('scio_token');

        if (!empty($cachedToken)) {
            return $cachedToken;
        }

        $response = Http::timeout(5)->get(
            "$this->baseURI/generatetoken",
            "client_id=$this->clientID&client_secret=$this->clientSecret"
        );

        if ($response->failed()) {
            // TODO: Handle this.
            return null;
        }

        // Get the response.
        $json = $response->json();
        $accessToken = $json["access_token"];
        $expiresIn = $json["expires_in"];

        if (!empty($accessToken)) {
            $ttl = 3600;
            if (!empty($expiresIn) && $expiresIn > 3600) {
                $ttl = $expiresIn - 3600;
            }
            Cache::put('scio_token', $accessToken, $ttl);
        }

        return $accessToken;
    }

    public function callAgrovok(ListAgrovokKeywordsRequest $request)
    {
        $token = $this->getToken();
        $search = $request->search;

        $response = Http::timeout(5)
            ->acceptJson()
            ->asJson()
            ->withToken($token)
            ->asForm()
            ->get("$this->baseURI/autocompleteagrovoc", [
                'autocomplete' => $search,
                'language' => 'en'
            ]);

        // Unwrap the outer array.
        $json = $response->json()[0];

        return $json;
    }
}
