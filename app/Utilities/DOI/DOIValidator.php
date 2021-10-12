<?php

namespace App\Utilities\DOI;

use App\Enums\HttpStatus;
use Exception;
use Http;

class DOIValidator
{

    public const PROVIDER_CROSSREF = 'crossref';
    public const PROVIDER_DATACITE = 'datacite';
    public const VALID_PROVIDERS = [self::PROVIDER_CROSSREF, self::PROVIDER_DATACITE];

    public function checkValidDoiProvider($doi)
    {
        // Check for a valid DOI provider.
        $response = Http::timeout(env('REQUEST_TIMEOUT', 5))
            ->get("https://api.crossref.org/works/$doi/agency");

        if ($response->status() === HttpStatus::NOT_FOUND) {
            throw new Exception('An invalid DOI was provided.');
        }

        $doiProvider = $response->json('message.agency.id');

        if (!in_array($doiProvider, self::VALID_PROVIDERS)) {
            throw new Exception('Unknown DOI service provider.');
        }

        return $doiProvider;
    }

    public function matchesTitle($doi, $title, $provider)
    {
        $url = '';
        if ($provider === self::PROVIDER_CROSSREF) {
            $url = "https://api.crossref.org/works/$doi";
        } else if ($provider === self::PROVIDER_DATACITE) {
            $url = "https://api.datacite.org/dois/application/vnd.datacite.datacite+json/$doi";
        }

        $response = Http::timeout(env('REQUEST_TIMEOUT', 5))->get($url);
        if ($response->failed()) {
            throw new Exception("Failed to get response from provider.");
        }

        $titles = [];
        if ($provider === self::PROVIDER_CROSSREF) {
            $titles = $response->json('message.title');
        } else if ($provider === self::PROVIDER_DATACITE) {
            $list = $response->json('titles');
            foreach ($list as $t) {
                $titles[] = $t["title"];
            }
        }

        return in_array($title, $titles);
    }

    public function validate($doi = '', $title = '')
    {
        if (empty($doi)) {
            throw new Exception('An empty DOI was provided.');
        }

        if (empty($title)) {
            throw new Exception('No resource title was provided.');
        }

        $provider = $this->checkValidDoiProvider($doi);

        $matchesTitle = $this->matchesTitle($doi, $title, $provider);

        return ['provider' => $provider, 'verified' => true, 'matchesTitle' => $matchesTitle];
    }
}