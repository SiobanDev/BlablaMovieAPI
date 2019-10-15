<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\OmdbApiService;
use App\Service\Vote\CheckService;
use App\Service\Vote\VoteService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
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
    //$maxMoviesNumber is set in service.yaml
    private $maxMoviesNumber;
    private $voteService;

    /**
     * UserController constructor.
     * @param int $maxMoviesNumber
     * @param SerializerInterface $serializer
     * @param VoteService $voteService
     */
    public function __construct(
        int $maxMoviesNumber,
        SerializerInterface $serializer,
        VoteService $voteService)
    {
        $this->serializer = $serializer;
        $this->maxMoviesNumber = $maxMoviesNumber;
        $this->voteService = $voteService;
    }

    /**
     * @Rest\Get("/", name="accueil")
     */
    public function accueilDoc()
    {
        return new Response('Bienvenue sur BlablaMovie API !');
    }

    /**
     * To test the function with Postman, you need to set a 'page' key in the query params.
     *
     * @Rest\Get("/movies", name="movies")
     * @param OmdbApiService $omdbApiService
     * @param Request $query
     * @return JsonResponse
     */
    public function toShowMovies(OmdbApiService $omdbApiService, Request $query)
    {
        $moviesData = $omdbApiService->showMovies($query);

        return new JsonResponse($moviesData, 200, [], true);
    }

    /**
     * To test the function with Postman, you need to set a 'imdbID' key in the body parameter form-data.
     *
     * @Rest\Post("/vote", name="add_vote")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param CheckService $checkService
     * @param VoteRepository $voteRepository
     * @return JsonResponse
     */
    public function toAddVote(
        Request $request,
        ValidatorInterface $validator,
        CheckService $checkService,
        VoteRepository $voteRepository)
    {
        $user = $this->getUser();

        $movieId = $request->request->get('imdbID');
        //$checkVote is the number of votes for the connected user for the current week
        $checkVote = $checkService->getWeekNumberVotations($voteRepository, $user);

        if ($checkVote < $this->maxMoviesNumber) {
            //Return a vote object in success and a string if no vote has been registered in the BDD
            $newVoteResult = $this->voteService->addVote($validator, $this->getUser(), $movieId, $voteRepository);

            if (is_object($newVoteResult)) {

                return new JsonResponse(
                    $this->serializer->serialize(
                        $newVoteResult,
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

            } else if (is_string($newVoteResult)) {

                return new JsonResponse($newVoteResult);

            } else if (empty($newVoteResult)) {

                return new JsonResponse('The vote has not been well registered in the DBB.');

            }
        } //Don't allow the vote if the user has already voted three times in the current week (calendar week !)
        else {

            $weekVotes = $checkService->getWeekVotations($voteRepository, $user);

            $displayAllVotes = $this->voteService->displayWeekVotes($user);


            $weekVotesJson = $this->serializer->serialize(
                $weekVotes,
                'json',
                [
                    'groups' => [
                        Vote::GROUP_SELF,
                        Vote::GROUP_VOTER,
                        User::GROUP_SELF,
                    ]
                ]
            );

            return new JsonResponse('The user has already voted for three movies : ' . $weekVotesJson, Response::HTTP_BAD_REQUEST, [], true);
        }
    }

    /**
     * To test the function with Postman, you need to set a 'vote_id' key in the headers parameters
     *
     * @Rest\Delete("/vote", name="remove_vote")
     * @param Request $request
     * @param VoteRepository $voteRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function toRemoveOneVote(
        Request $request,
        VoteRepository $voteRepository,
        EntityManagerInterface $entityManager
    )
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $votationId = $request->headers->get('vote_id');

        $voteToDelete = $voteRepository->findOneByIdAndUserId($votationId, $userId);

        if (empty($voteToDelete)) {
            throw new Exception('There is no vote to delete or you have no right to do it.', Response::HTTP_FORBIDDEN);
        }

        $user->removeVotation($voteToDelete);
        $entityManager->flush();

        return new JsonResponse(
            'The vote has been well removed.',
            Response::HTTP_RESET_CONTENT
        );

    }
}