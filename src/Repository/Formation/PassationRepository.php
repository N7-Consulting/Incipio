<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Passation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Passation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Passation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Passation[]    findAll()
 * @method Passation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PassationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Passation::class);
    }

    // /**
    //  * @return Passation[] Returns an array of Passation objects
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
    public function findOneBySomeField($value): ?Passation
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
