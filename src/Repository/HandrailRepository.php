<?php

namespace App\Repository;

use App\Entity\Handrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Handrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method Handrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method Handrail[]    findAll()
 * @method Handrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HandrailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Handrail::class);
    }

    // /**
    //  * @return Handrail[] Returns an array of Handrail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function recent($max, $projects)
    {
        return $this->createQueryBuilder('h')
            ->where('h.status = 0')
            ->andWhere('h.project IN (:projects) OR h.project = 1')
            ->orderBy('h.createdAt', 'DESC')
            ->setParameter('projects', $projects)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult()
        ;
    }

    public function search($value, $projects)
    {
        return $this->createQueryBuilder('h')
            ->where('h.status = 0 AND (h.subject LIKE :value OR h.content LIKE :value)')
            ->andWhere('h.project IN (:projects) OR h.project = 1')
            ->orderBy('h.createdAt', 'DESC')
            ->setParameters(array(
                'projects' => $projects,
                'value' => "%".$value."%"
            ))
            ->getQuery()
            ->getResult()
            ;
    }

}
