<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\OmdbApiService;
use App\Service\Vote\addVoteService;
use App\Service\Vote\checkVoteService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
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
     * @param OmdbApiService $omdbApiService
     * @return JsonResponse
     */
    public function showMovies(OmdbApiService $omdbApiService)
    {
        $moviesData = $omdbApiService->getMovies();

        return new JsonResponse(($this->serializer->serialize($moviesData, 'json')), 200, [], true);
    }

    /**
     * @Rest\Post("/movies", name="vote")
     * @param Request $voteRequest
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param VoteRepository $voteRepository
     * @param checkVoteService $checkVoteService
     * @param addVoteService $voteService
     * @return JsonResponse
     */
    public function saveVote(
        Request $voteRequest,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        VoteRepository $voteRepository,
        checkVoteService $checkVoteService,
        addVoteService $voteService
    )
    {
        if ($checkVoteService) {

            $voteData = $voteService->addVotation($voteRequest, $validator, $entityManager, $this->getUser(), $voteRepository);

            return new JsonResponse(
                $this->serializer->serialize(
                    $voteData,
                    'json',
                    [
                        'groups' => [
                            Vote::GROUP_SELF,
                            Vote::GROUP_VOTER,
                            User::GROUP_SELF,
                        ]
                    ]
                ),
                Response::HTTP_CREATED,
                [],
                true
            );
        }

        return new JsonResponse('Three votes have already be submitted. Wait the next week.', 'json');
    }

}