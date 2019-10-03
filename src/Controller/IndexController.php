<?php

namespace App\Controller;

use App\Service\OmdbApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    /**
     * @Rest\Get("/", name="accueil")
     */
    public function accueilDoc()
    {
        return new Response('Bienvenue sur BlablaMovie API !');
    }

    /**
     * @Rest\Get("/movies", name="movies")
     */
    public function displayMovies()
    {
        $omdbapiservice = new OmdbApiService();
        return new JsonResponse();
    }
}