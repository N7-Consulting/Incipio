<?php

namespace App\Repository\Hr;

use App\Entity\Hr\AlumnusContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AlumnusContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlumnusContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlumnusContact[]    findAll()
 * @method AlumnusContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlumnusContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlumnusContact::class);
    }

    /**
     * @return array
     */
    public function findAllByAlumnus()
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('f')->from(AlumnusContact::class, 'f')
            ->orderBy('f.date', 'desc');
        $entities = $query->getQuery()->getResult();

        $contactsParAlumnus = [];
        /** @var AlumnusContact $AlumnusContact */
        foreach ($entities as $contact) {
            $nom = $contact->getAlumnus()->getPersonne()->getPersonne()->getPrenomNom();
            if (array_key_exists($nom, $contactsParAlumnus)) {
                $contactsParAlumnus[$nom][] = $contact;
            } else {
                $contactsParAlumnus[$nom] = [$contact];
            }
        }       
        return $contactsParAlumnus;
    }

    /**
     * @return array
     */
    public function findAllByDernierContact()
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('f')->from(AlumnusContact::class, 'f')
            ->orderBy('f.id', 'asc');
        $entities = $query->getQuery()->getResult();

        $dernierContactParAlumnus = [];
        /** @var AlumnusContact $AlumnusContact */
        foreach ($entities as $contact) {
            $nom = $contact->getAlumnus()->getPersonne()->getPersonne()->getPrenomNom();
            if (array_key_exists($nom, $dernierContactParAlumnus)) {
                if ($dernierContactParAlumnus[$nom] < $contact->getDate()) {
                    $dernierContactParAlumnus[$nom] = $contact->getDate();
                }
            } else {
                $dernierContactParAlumnus[$nom] = $contact->getDate();
            }
        }       
        return $dernierContactParAlumnus;
    }
}
