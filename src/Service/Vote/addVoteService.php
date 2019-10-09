<?php

namespace App\Service\Vote;

use App\Entity\Vote;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class addVoteService
{
    public function addVotation(Request $voteRequest, ValidatorInterface $validator, EntityManagerInterface $entityManager, $user, VoteRepository $voteRepository)
    {
        $vote = new Vote();

        $movieId = $voteRequest->request->get('imdbID');
        $searchVoteService = $voteRepository->findByMobieId($movieId);

        if(!$searchVoteService) {
            $vote->setMovieId($movieId);
            $vote->setVotationDate(new DateTime());
            $vote->setVoter($user);

            $errors = $validator->validate($vote);

            if (count($errors) > 0) {
                /*
                 * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
                 */
                $errorsString = (string)$errors;

                return $errorsString;
            }

            // tell Doctrine you want to (eventually) save the vote (no queries yet)
            $entityManager->persist($vote);
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return $vote;
        }
        return "User has already voted for this movie ID.";
    }
}