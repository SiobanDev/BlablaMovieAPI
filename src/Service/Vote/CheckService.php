<?php


namespace App\Service\Vote;

use App\Repository\VoteRepository;

class CheckService
{
//Calculate the date of the week from a current date
    public function whichWeekService()
    {
        $votationDate = new \DateTime();
        $voteDatetoString = $votationDate->format('Y-m-d');
        //dd($voteDatetoString);
        $timeStampVoteDate = strtotime($voteDatetoString);
        $day = date('w', $timeStampVoteDate);

        //search for the number
        $numWeekVoteDate = date('W', $timeStampVoteDate);
        //dd($numWeekVoteDate);

        //recherche du lundi de la semaine en fonction de la ligne précédente
        $firstDayOfTheWeek = ($day == 1) ? date('Y-m-d', $timeStampVoteDate) : date('Y-m-d', strtotime('last monday', $timeStampVoteDate));
        $lastDayOfTheWeek = ($day == 1) ? date('Y-m-d', $timeStampVoteDate) : date('Y-m-d', strtotime('next sunday', $timeStampVoteDate));

        //dd("Le premier jour de la semaine $numWeekVoteDate est le $firstDayOfTheWeek<br>");
        //dd("Le dernier jour de la semaine $numWeekVoteDate est le $lastDayOfTheWeek<br>");

        return $lastDayOfTheWeek;
    }

    public function getWeekNumberVotations(VoteRepository $voteRepository)
    {
        //Get the sunday's date of the vote's week
        $sundayDate = $this->whichWeekService();

        //Get the votations of the week (in the DBB)
        $votesOfTheWeek = $voteRepository->findByVotationDate($sundayDate);

        //Check if there is 3 votations in the week for the connected user
        return count($votesOfTheWeek);
    }
}