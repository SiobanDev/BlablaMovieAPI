<?php

namespace App\Service\Vote;

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
        if (is_null($identicalVote)) {
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

//    public
//    function removeOneVote(VoteRepository $voteRepository, int $votationId, $connectedUser)
//    {
//        dump($connectedUser->getVotations());
//        $userId = $connectedUser->getId();
//
//        $voteToDelete = $voteRepository->findOneByIdAndUserId($votationId, $userId);
//
//        if (isset($voteToDelete)) {
//            $connectedUser->removeVotation($voteToDelete);
//            $this->entityManager->remove($voteToDelete);
//
//            // actually executes the queries (i.e. the INSERT query)
//            $this->entityManager->flush();
//
//            $deletedVoteResearch = $voteRepository->findOneByIdAndUserId($votationId, $userId);
//
//            return $deletedVoteResearch;
//
//        } else {
//
//            return "There is no vote to delete or you have no right to do it.";
//        }
    }


    public
    function removeAllVotes($user)
    {
        //$movieVotesToDelete is an array
        $movieVotesToDelete = $this->voteRepository->findByUser($user);

        foreach ($movieVotesToDelete as $movieVoteItem) {

            // tell Doctrine you want to (eventually) remove the vote (no queries yet)
            $this->entityManager->remove($movieVoteItem);
            // actually executes the queries (i.e. the INSERT query)
            $this->entityManager->flush();

            return $movieVoteItem;
        }
    }
}