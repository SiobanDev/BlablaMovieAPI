<?php

namespace App\Service\Vote;

use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function addVotation(ValidatorInterface $validator, $connectedUser, $movieId)
    {
        $vote = new Vote();
        $currentDate = new \DateTime();
        //$checkVote returns false if the user has already voted for three movies in the current week (calendar week !)

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

        return $vote;
    }

    public function removeVotation($connectedUser, $movieId)
    {
        $connectedUserId = $connectedUser->getId();

        //$movieVotesToDelete is an array
        $movieVotesToDelete = $this->voteRepository->findByVoterIdAndMovieId($connectedUserId, $movieId);

        foreach ($movieVotesToDelete as $movieVoteItem) {

            // tell Doctrine you want to (eventually) remove the vote (no queries yet)
            $this->entityManager->remove($movieVoteItem);
            // actually executes the queries (i.e. the INSERT query)
            $this->entityManager->flush();

            return $movieVoteItem;
        }
    }
}