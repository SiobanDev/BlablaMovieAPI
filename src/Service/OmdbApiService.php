<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class OmdbApiService
{
    /** @var string */
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function displayAll()
    {
        $client = HttpClient::create();

        try {
            $response = $client->request('GET', 'http://www.omdbapi.com/?apikey=' . $this->apiKey . '&s=space');

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                return $response->getContent();
            }

        } catch (TransportExceptionInterface $e) {
            throw new Exception($e);
        }
    }
}