<?php

namespace App\Repository;

use App\Entity\NotifUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NotifUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifUser[]    findAll()
 * @method NotifUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifUser::class);
    }

    // /**
    //  * @return NotifUser[] Returns an array of NotifUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotifUser
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
