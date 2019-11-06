<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\Vote\WeekService;
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

class VoteController extends AbstractController
{
    private $serializer;
    //$maxMoviesNumber is set in service.yaml
    private $maxMoviesNumber;
    private $voteService;

    /**
     * @var VoteRepository
     */
    private $voteRepository;

    /**
     * VoteController constructor.
     * @param int $maxMoviesNumber
     * @param SerializerInterface $serializer
     * @param VoteService $voteService
     * @param Request $request
     * @param VoteRepository $voteRepository
     */
    public function __construct(
        int $maxMoviesNumber,
        SerializerInterface $serializer,
        VoteService $voteService,
        VoteRepository $voteRepository)
    {
        $this->serializer = $serializer;
        $this->maxMoviesNumber = $maxMoviesNumber;
        $this->voteService = $voteService;
        $this->voteRepository = $voteRepository;
    }

    /**
     * To test the function with Postman, you need to set a 'imdbID' key in the body parameter form-data.
     *
     * @Rest\Post("/vote", name="add_vote")
     * @param ValidatorInterface $validator
     * @param WeekService $checkService
     * @return JsonResponse
     */
    public function add(
        Request $request,
        ValidatorInterface $validator,
        WeekService $checkService
    )
    {
        $user = $this->getUser();

        $movieId = $request->request->get('imdbID');
        //$checkVote is the number of votes for the connected user for the current week
        $checkVote = $checkService->getWeekVotationsNumber($this->voteRepository, $user);

        if ($checkVote < $this->maxMoviesNumber) {
            //Return a vote object in success and a string if no vote has been registered in the BDD
            $newVoteResult = $this->voteService->add($validator, $this->getUser(), $movieId, $this->voteRepository);

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

            $weekVotes = $checkService->getWeekVotations($this->voteRepository, $user);

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
     * To test the function with Postman, you need to set a 'imdbID' key in the body parameter form-data.
     *
     * @Rest\Get("/votes", name="all_votes")
     * @return JsonResponse
     */
    public function displayAll()
    {
        $user = $this->getUser();
        $displayAllVotes = $this->voteService->displayWeekVotes($user);

        return new JsonResponse($this->serializer->serialize($displayAllVotes, 'json'), Response::HTTP_OK, [], true);

    }


    /**
     * To test the function with Postman, you need to set a 'vote_id' key in the headers parameters
     *
     * @Rest\Delete("/vote", name="remove_vote")
     * @param EntityManagerInterface $entityManager
     * @param WeekService $checkService
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function removeOne(
        Request $request,
        EntityManagerInterface $entityManager,
        WeekService $checkService
    )
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $actionDay = new \DateTime();
        $votationDate = $request->headers->get('votation_date');
        $votationId = $request->headers->get('vote_id');
        $voteToDelete = $this->voteRepository->findOneByIdAndUserId($votationId, $userId);

        //Check if the vote to delete is in the BDD
        if (empty($voteToDelete)) {
            throw new Exception('There is no vote to delete or you have no right to do it.', Response::HTTP_FORBIDDEN);
        }

        //Week number of the action date
        $actionWeekNumber = $checkService->whichWeek($actionDay);

        //Week number of the vote to delete
        $voteWeekNumber = $checkService->whichWeek($votationDate);

        //Check if the vote to delete has been registered during the current week.
        if ($actionWeekNumber !== $voteWeekNumber) {
            throw new Exception('It is not possible to delete any vote of the previous weeks.', Response::HTTP_FORBIDDEN);
        }

        $user->removeVotation($voteToDelete);
        $entityManager->flush();

        return new JsonResponse(
            'The vote has been well removed.',
            Response::HTTP_RESET_CONTENT
        );

    }
}