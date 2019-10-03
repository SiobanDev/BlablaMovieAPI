<?php

namespace App\Service;

use JMS\Serializer\Serializer;

class OmdbApiService
{
    private $omdbApiClient;
    private $serializer;
    private $apiKey;

    public function __construct(Client $omdbApiClient, Serializer $serializer, $apiKey)
    {
        $this->omdbApiClient = $omdbApiClient;
        $this->serializer = $serializer;
        $this->apiKey = $apiKey;
    }

    public function getCurrent($movie_title)
    {
        $uri = 'http://www.omdbapi.com/?apikey=' . $this->apiKey . '&t=' . $movie_title;
        
        curl_init($uri);

        $response = $this->omdbApiClient->get($uri);

        $data = $this->serializer->deserialize($response->getBody()->getContents(), 'array', 'json');

        return [
            'city' => $data['name'],
            'description' => $data['weather'][0]['main']
        ];

        // create curl resource 
        $uri = curl_init('http://www.omdbapi.com/');

        // set url 
        curl_setopt($uri, CURLOPT_URL, "example.com");

        //return the transfer as a string 
        curl_setopt($uri, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string 
        $output = curl_exec($uri);

        // close curl resource to free up system resources 
        curl_close($uri);
    }
}
