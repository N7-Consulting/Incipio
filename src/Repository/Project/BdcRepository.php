<?php

namespace App\Repository\Project;

use App\Entity\Project\Bdc;
use Doctrine\ORM\EntityRepository;

class BdcRepository extends EntityRepository
{
  public function findAllByCca($id)
  {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('b')
    ->from(Bdc::class, 'b')
    ->where('b IS NOT NULL')
    ->join('b.etude', 'e')
    ->where('e.cca = :id')
    ->setParameter('id', $id )
    ->orderBy('b.dateSignature', 'DESC');
    $query = $qb->getQuery();

    return $query->getResult();
  }
}
