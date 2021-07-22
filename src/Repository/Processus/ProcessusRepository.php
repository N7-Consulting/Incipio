<?php

namespace App\Repository\Processus;

use App\Entity\Processus\Processus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Processus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Processus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Processus[]    findAll()
 * @method Processus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Processus::class);
    }

    // /**
    //  * @return Processus[] Returns an array of Processus objects
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
    public function findOneBySomeField($value): ?Processus
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
