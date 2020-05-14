<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, File::class);
    }

    // /**
    //  * @return File[] Returns an array of File objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function recent($max, $projects)
    {
        return $this->createQueryBuilder('f')
            ->where('f.status = 0')
            ->andWhere('f.project IN (:projects) OR f.project = 1')
            ->orderBy('f.createdAt', 'DESC')
            ->setParameter('projects', $projects)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult()
            ;
    }

    public function search($value, $projects)
    {
        return $this->createQueryBuilder('f')
            ->where('f.status = 0 AND (f.subject LIKE :value)')
            ->andWhere('f.project IN (:projects) OR f.project = 1')
            ->orderBy('f.createdAt', 'DESC')
            ->setParameters(array(
                'projects' => $projects,
                'value' => "%".$value."%"
            ))
            ->getQuery()
            ->getResult()
            ;
    }
}
