<?php

namespace App\Utilities\SCIO;

use Cache;
use Exception;
use Http;

class TokenGenerator
{
    protected $baseURI;
    protected $clientID;
    protected $clientSecret;
    protected $cacheKey;

    public function __construct()
    {
        $this->clientID = env('SCIO_SERVICES_CLIENT_ID');
        $this->clientSecret = env('SCIO_SERVICES_SECRET');
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL');
        $this->cacheKey = env('SCIO_CACHE_TOKEN_KEY');
    }

    public function getToken()
    {
        if (Cache::has($this->cacheKey)) {
            return Cache::get($this->cacheKey);
        }

        $response = Http::timeout(env('REQUEST_TIMEOUT_SECONDS'))->get(
            "$this->baseURI/generatetoken",
            "client_id=$this->clientID&client_secret=$this->clientSecret"
        );

        if ($response->failed()) {
            throw new Exception('Failed to get token.');
        }

        // Get the response.
        $json = $response->json('response');
        $accessToken = $json["access_token"];
        $expiresIn = $json["expires_in"];

        if (!empty($accessToken)) {
            $ttl = env('CACHE_TTL_SECONDS');
            if (!empty($expiresIn) && $expiresIn > env('CACHE_TTL_SECONDS')) {
                $ttl = $expiresIn - env('CACHE_TTL_SECONDS');
            }
            Cache::put($this->cacheKey, $accessToken, $ttl);
        }

        return $accessToken;
    }
}
