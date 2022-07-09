<?php

namespace App\Repository;

use App\Entity\Pannier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pannier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pannier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pannier[]    findAll()
 * @method Pannier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PannierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pannier::class);
    }

    // /**
    //  * @return Pannier[] Returns an array of Pannier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pannier
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
