<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Project;

use App\Entity\Project\Cca;
use App\Entity\Project\DocType;
use App\Entity\Project\Etude;
use Doctrine\ORM\EntityManagerInterface;

class DocTypeManager /*extends \Twig_Extension*/
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // Pour utiliser les fonctions depuis twig
    public function getName()
    {
        return 'Project_DocTypeManager';
    }

    // Pour utiliser les fonctions depuis twig
    public function getFunctions()
    {
        return [];
    }

    public function getRepository()
    {
        return $this->em->getRepository(Etude::class);
    }

    public function checkSaveNewEmploye(DocType $doc)
    {
        if (!$doc->isKnownSignataire2()) {
            $employe = $doc->getNewSignataire2();
            $this->em->persist($employe->getPersonne());
            if ($doc instanceof Cca) {
                $employe->setProspect($doc->getProspect());
            } else {
                $employe->setProspect($doc->getEtude()->getProspect());
            }
            $employe->getPersonne()->setEmploye($employe);
            $this->em->persist($employe);

            $doc->setSignataire2($employe->getPersonne());
        }
    }
}
