<?php

namespace App\Controller;

use App\Service\OmdbApiService;
use App\Service\VoteService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IndexController extends AbstractController
{
    private $serializer;

    /**
     * UserController constructor.
     * @param $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/", name="accueil")
     */
    public function accueilDoc()
    {
        return new Response('Bienvenue sur BlablaMovie API !');
    }

    /**
     * @Rest\Get("/movies", name="movies")
     * @return JsonResponse
     */
    public function showMovies()
    {
        $omdbapiService = new OmdbApiService();
        $moviesData = $omdbapiService->getMovies();

        return new JsonResponse(($this->serializer->serialize($moviesData, 'json')), 200, [], true);
    }

    /**
     * @Rest\Post("/movies", name="vote")
     * @param Request $voteRequest
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param UserInterface $user
     * @return JsonResponse
     */
    public function saveVote(Request $voteRequest, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $voteService = new VoteService();
        $voteData = $voteService->addVotation($voteRequest, $validator, $entityManager, $this->getUser());
        //dd($voteData);

        return new JsonResponse($this->serializer->serialize($voteData, 'json'));
    }
}