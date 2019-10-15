<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\OmdbApiService;
use App\Service\Vote\CheckService;
use App\Service\Vote\VoteService;
use Cassandra\Exception\UnauthorizedException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
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

    /**
     * UserController constructor.
     * @param SerializerInterface $serializer
     * @param int $maxMoviesNumber
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
    public function toShowMovies(OmdbApiService $omdbApiService)
    {
        $moviesData = $omdbApiService->showMovies();

        return new JsonResponse($moviesData, 200, [], true);
    }

    /**
     * To test the function with Postman, you need to set a 'imdbID' key in the body parameter form-data.
     *
     * @Rest\Post("/vote", name="add_vote")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param VoteService $voteService
     * @param CheckService $checkService
     * @param VoteRepository $voteRepository
     * @return JsonResponse
     */
    public function toAddVote(
        Request $request,
        ValidatorInterface $validator,
        VoteService $voteService,
        CheckService $checkService,
        VoteRepository $voteRepository)
    {
        $user = $this->getUser();
        $movieId = $request->request->get('imdbID');
        //$checkVote is the number of votes for the connected user for the current week
        $checkVote = $checkService->getWeekNumberVotations($voteRepository, $user);

        if ($checkVote < $this->maxMoviesNumber) {
            //Return a vote object in success and a string if no vote has been registered in the BDD
            $newVoteResult = $voteService->addVote($validator, $this->getUser(), $movieId, $voteRepository);

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

            } else if (is_null($newVoteResult)) {

                return new JsonResponse('The vote has not been well registered in the DBB.');

            }
        } //Don't allow the vote if the user has already voted three times in the current week (calendar week !)
        else {
            return new JsonResponse('The user has already voted for three movies.', Response::HTTP_BAD_REQUEST);
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

        if (is_null($voteToDelete)) {
            throw new UnauthorizedException('There is no vote to delete or you have no right to do it.', Response::HTTP_FORBIDDEN, '');
        }

        $user->removeVotation($voteToDelete);
        $entityManager->flush();

        return new JsonResponse(
            'The vote has been well removed.',
            Response::HTTP_NO_CONTENT
        );

    }
}