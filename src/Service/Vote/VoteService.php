<?php

namespace App\Service\Vote;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VoteService
{
    private $voteRepository;
    private $checkService;
    private $entityManager;

    public function __construct(VoteRepository $voteRepository, CheckService $checkService, EntityManagerInterface $entityManager)
    {
        $this->voteRepository = $voteRepository;
        $this->checkService = $checkService;
        $this->entityManager = $entityManager;
    }

    public function addVote(ValidatorInterface $validator, UserInterface $connectedUser, string $movieId, VoteRepository $voteRepository)
    {
        $userId = $connectedUser->getId();
        $vote = new Vote();
        $currentDate = new \DateTime();

        //Get the vote with the same parameters as those from the request
        $identicalVote = $voteRepository->findOneByMovieIdAndUserId($movieId, $userId);

        //Check if there's already a vote with the same parameters as those from the request and prevent the creation if it's the case
        if (empty($identicalVote)) {
            $vote->setMovieId($movieId);

            //$dateTime->createFromFormat();
            $vote->setVotationDate($currentDate);
            $vote->setVoter($connectedUser);

            $errors = $validator->validate($vote);

            if (count($errors) > 0) {
                /*
                 * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
                 */
                $errorsString = (string)$errors;

                return $errorsString;
            }

            // tell Doctrine you want to (eventually) save the vote (no queries yet)
            $this->entityManager->persist($vote);

            // actually executes the queries (i.e. the INSERT query)
            $this->entityManager->flush();

            $newVote = $voteRepository->findOneByMovieIdAndUserId($movieId, $userId);

            return $newVote;

        } else {

            return "This user has already voted for this movie.";
        }
    }

    public function displayWeekVotes($user)
    {
        $userId = $user->getId();
        //$votesToDisplay is an array
        $votesToDisplayResults = $this->checkService->getWeekVotations($this->voteRepository, $user);

        return $votesToDisplayResults;
    }

    public function removeAllVotes($user)
    {
        $userId = $user->getId();
        //$votesToDelete is an array
        $votesToDeleteResults = $this->voteRepository->findByUser($userId);

        foreach ($votesToDeleteResults as $votesToDeleteItem) {

            $user->removeVotation($votesToDeleteItem);
            // actually executes the queries (i.e. the INSERT query)
            $this->entityManager->flush();
        }
        return null;
    }
}