<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

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
     * Get all the votations of a user
     * @param $userId
     * @return mixed
     */
    public function findByUser($userId)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.voter = :currentUser')
            ->setParameter('currentUser', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get a votation of a user by its votation id
     * @param $votationId
     * @param $userId
     * @return Vote|null
     * @throws NonUniqueResultException
     */
    public function findOneByIdAndUserId($votationId, $userId): ?Vote
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id = :votationId')
            ->setParameter('votationId', $votationId)
            ->andWhere('q.voter = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get a votation by the movie id and the voter id
     * @param $votationId
     * @param $userId
     * @return Vote|null
     * @throws NonUniqueResultException
     */
    public function findOneByMovieIdAndUserId($movieId, $userId): ?Vote
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.movieId = :movieId')
            ->setParameter('movieId', $movieId)
            ->andWhere('q.voter = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Get all the votations of a user during the whole week of a current date.
     * @param $endOfTheWeekDate
     * @param $userId
     * @return Vote[] Returns an array of Vote objects
     */
    public function findByVoteDateAndUserId($endOfTheWeekDate, $userId)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('date(q.votationDate) >= datesub(:endOfTheWeekDate, 1, \'WEEK\')')
            ->setParameter('endOfTheWeekDate', $endOfTheWeekDate)
            ->andWhere('q.voter = :userId')
            ->setParameter('userId', $userId)
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
