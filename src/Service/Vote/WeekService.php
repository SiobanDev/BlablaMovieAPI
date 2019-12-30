<?php


namespace App\Service\Vote;

use App\Repository\VoteRepository;

class WeekService
{
//Calculate the date of the week from a current date
    public function whichWeek()
    {
        $voteDatetoString = (new \DateTime())->format('Y-m-d');

        //Function date() need a timestamp as second parameter and return the number of the calendar week based on the given date
        $timeStampVoteDate = strtotime($voteDatetoString);
        $Weekday = date('w', $timeStampVoteDate);
        $numOfTheWeekBasedOnADate = date('W', $timeStampVoteDate);

        //recherche des dates du lundi et du dimanche de la semaine
        $firstDayOfTheWeek = ($Weekday == 1) ? date('Y-m-d', $timeStampVoteDate) : date('Y-m-d', strtotime('last monday', $timeStampVoteDate));
        $lastDayOfTheWeek = ($Weekday == 1) ? date('Y-m-d', $timeStampVoteDate) : date('Y-m-d', strtotime('next sunday', $timeStampVoteDate));

        //dd("Le premier jour de la semaine $numOfTheWeekBasedOnADate est le $firstDayOfTheWeek<br>");
        //dd("Le dernier jour de la semaine $numOfTheWeekBasedOnADate est le $lastDayOfTheWeek<br>");

        return $lastDayOfTheWeek;
    }

    public function getWeekVotationsNumber(VoteRepository $voteRepository, $user)
    {
        $userId = $user->getId();
        //Get the sunday's date of the vote's week
        $sundayDate = $this->whichWeek();

        //Get the votations of the week (in the DBB)
        $votesOfTheWeekList = $voteRepository->findByVoteDateAndUserId($sundayDate, $userId);

        //Check if there is 3 votations in the week for the connected user
        return count($votesOfTheWeekList);
    }

    public function getWeekVotations(VoteRepository $voteRepository, $user)
    {
        $userId = $user->getId();
        //Get the sunday's date of the vote's week
        $sundayDate = $this->whichWeek();

        //Get the votations of the week (in the DBB)
        $votesOfTheWeekList = $voteRepository->findByVoteDateAndUserId($sundayDate, $userId);

        //Check if there is 3 votations in the week for the connected user
        return $votesOfTheWeekList;
    }
}