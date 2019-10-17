<?php

namespace App\Controller;

use App\Service\OmdbApiService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MovieController extends AbstractController
{
    /**
     * To test the function with Postman, you need to set a 'page' key in the query params.
     *
     * @Rest\Get("/movies", name="movies")
     * @param OmdbApiService $omdbApiService
     * @return JsonResponse
     */
    public function displayAll(OmdbApiService $omdbApiService)
    {
        $moviesData = $omdbApiService->displayAll();

        return new JsonResponse($moviesData, 200, [], true);
    }
}