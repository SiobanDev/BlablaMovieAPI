<?php

namespace App\Service;

namespace App\Service;

use App\Entity\Vote;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VoteService
{
    //$voteRequest should contain Json data for a futur new Vote object
    public function addVotation(Request $voteRequest, ValidatorInterface $validator, EntityManagerInterface $entityManager, $user)
    {
        $vote = new Vote();

        $movieId = $voteRequest->request->get('imdbID');
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
}