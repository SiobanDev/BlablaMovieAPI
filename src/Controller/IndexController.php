<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{

    /**
     * @Rest\Get("/", name="home")
     */
    public function index()
    {
        return new Response('Bienvenue sur BlablaMovie API !');
    }
}