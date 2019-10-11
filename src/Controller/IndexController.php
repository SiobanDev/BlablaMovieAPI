<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\OmdbApiService;
use App\Service\Vote\CheckService;
use App\Service\Vote\VoteService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IndexController extends AbstractController
{
    private $serializer;
    private $maxMoviesNumber;

    /**
     * UserController constructor.
     * @param SerializerInterface $serializer
     * @param $maxMoviesNumber
     */
    public function __construct(SerializerInterface $serializer, int $maxMoviesNumber)
    {
        $this->serializer = $serializer;
        $this->maxMoviesNumber = $maxMoviesNumber;
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

        return new JsonResponse($moviesData, 200, [], true);
    }

    /**
     * @Rest\Post("/votes", name="vote")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param VoteService $voteService
     * @param CheckService $checkService
     * @param VoteRepository $voteRepository
     * @return JsonResponse
     */
    public function manageVote(
        Request $request,
        ValidatorInterface $validator,
        VoteService $voteService,
        CheckService $checkService,
        VoteRepository $voteRepository)
    {
        //Allow to know if the user want to add or delete a vote
        $connectedUser = $this->getUser();
        $movieId = $request->request->get('imdbID');
        $checkVote = $checkService->getWeekNumberVotations($voteRepository);
        $adminEmail = $this->getParameter('app.admin_email');

        //To test with Postman, you need to set the key 'actionToDo' set to 'addVote' and the key 'imdbID' with an movie Id in the form-data.
        if ($checkVote < $this->maxMoviesNumber) {
            $addedVoteData = $voteService->addVotation($validator, $connectedUser, $movieId);

            return new JsonResponse(
                $this->serializer->serialize(
                    $addedVoteData,
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
        } //Don't allow the vote if the user has already voted three times in the current week (calendar week !)
        else  {
            return new JsonResponse('The user has already voted for three movies.', 400);
        }
    }

    /**
     * @Rest\Delete("/votes", name="vote")
     * @param Request $request
     * @param VoteService $voteService
     * @return JsonResponse
     */
    public function removeVote(
        Request $request,
        VoteService $voteService)
    {
        //Allow to know if the user want to add or delete a vote
        $connectedUser = $this->getUser();
        $movieId = $request->request->get('imdbID');

        $removedVoteData = $voteService->removeVotation($connectedUser, $movieId);

        return new JsonResponse(
            $this->serializer->serialize(
                $removedVoteData,
                'json'),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

}