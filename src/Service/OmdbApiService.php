<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class OmdbApiService
{
    public function getMovies()
    {
        $apiKey = getenv('OMDB_API_KEY');
        $client = HttpClient::create();

        $response = $client->request('GET', 'http://www.omdbapi.com/?apikey=' . $apiKey . '&s=space');

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            return $response->getContent();
        }

    }

}