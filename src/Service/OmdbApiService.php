<?php

namespace App\Service;

use http\QueryString;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class OmdbApiService
{
    /** @var string */
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function showMovies(Request $query)
    {
        $client = HttpClient::create();
        $pageNumber = $query->getCookie('page', QueryString::TYPE_INT, 1);

        $response = $client->request('GET', 'http://www.omdbapi.com/?apikey=' . $this->apiKey . '&s=space&page=' . $pageNumber);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            return $response->getContent();
        }

    }
}