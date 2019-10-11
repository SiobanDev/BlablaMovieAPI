<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    /**
     * Check the votations during the whole week of a current date.
     * @param $endOfTheWeekDate
     * @return Vote[] Returns an array of Vote objects
     */

    public function findByVotationDate($endOfTheWeekDate)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('date(v.votationDate) >= datesub(:endOfTheWeekDate, 1, \'WEEK\')')
            ->setParameter('endOfTheWeekDate', $endOfTheWeekDate)
            ->getQuery()
            ->getResult();
    }

    public function findByVoterIdAndMovieId($connectedUserId, $postMovieId)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.voter = :userId')
            ->setParameter('userId', $connectedUserId)
            ->andWhere('v.movieId = :postMovieId')
            ->setParameter('postMovieId', $postMovieId)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Vote
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
