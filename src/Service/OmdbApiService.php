<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class OmdbApiService
{
    /** @var string */
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function showMovies()
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'http://www.omdbapi.com/?apikey=' . $this->apiKey . '&s=space');

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            return $response->getContent();
        }

    }
}