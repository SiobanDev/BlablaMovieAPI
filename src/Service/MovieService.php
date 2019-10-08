<?php

namespace App\Service;

use App\Model\Movie;
use Symfony\Component\Serializer\Serializer;

class MovieService
{
    public function displayMovies()
    {
        $serializer = new Serializer();
        $omdbApiService = new OmdbApiService();
        $moviesJson = $omdbApiService->getMovies();

        $moviesObjects = $serializer->deserialize($moviesJson, Movie::class, 'json');
        var_dump($moviesObjects);
        return $moviesObjects;
    }
}