<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Entity\DocType;

class DocTypeManager /*extends \Twig_Extension*/
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    // Pour utiliser les fonctions depuis twig
    public function getName()
    {
        return 'mgate_DocTypeManager';
    }
    // Pour utiliser les fonctions depuis twig
    public function getFunctions()
    {
        return array();
    }

    public function getRepository()
    {
        return $this->em->getRepository('mgateSuiviBundle:Etude');
    }

    public function checkSaveNewEmploye(DocType $doc)
    {
        if (!$doc->isKnownSignataire2()) {
            $employe = $doc->getNewSignataire2();
            $this->em->persist($employe->getPersonne());
            $employe->setProspect($doc->getEtude()->getProspect());
            $employe->getPersonne()->setEmploye($employe);
            $this->em->persist($employe);

            $doc->setSignataire2($employe->getPersonne());

        }
    }
}
