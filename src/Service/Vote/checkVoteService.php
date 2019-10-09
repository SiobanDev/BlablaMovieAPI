<?php

namespace App\Service\Vote;

use App\Entity\Vote;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class checkVoteService
{
    public function checkVotation(Request $voteRequest, VoteRepository $voteRepository)
    {

    }
}