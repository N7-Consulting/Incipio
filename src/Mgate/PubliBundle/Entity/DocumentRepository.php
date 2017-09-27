<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\PubliBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DocumentRepository.
 */
class DocumentRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getTotalSize()
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->add('select', 'SUM(u.size)')
                    ->add('from', 'MgatePubliBundle:Document u');
        $result = $query->getQuery()->getResult();

        return $result[0] ? $result[0][1] : null;
    }
}
