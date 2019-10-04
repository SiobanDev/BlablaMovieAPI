<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class OmdbApiService
{
    public function getMovies()
    {
        $apiKey = getenv('OMDB_API_KEY');
        $client = HttpClient::create();

        try {
            $response = $client->request('GET', 'http://www.omdbapi.com/?apikey=' . $apiKey . '&s=space');

        } catch (TransportExceptionInterface $e) {
            throw $e;
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {

            $content = $response->getContent();

            return $content;
        }
    }
}
