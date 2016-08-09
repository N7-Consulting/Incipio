<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\SuiviBundle\Entity\Av;
use mgate\SuiviBundle\Form\AvType;

class PhaseChange
{
    private $position = false;
    private $nbrJEH = false;
    private $prixJEH = false;
    private $titre = false;
    private $objectif = false;
    private $methodo = false;
    private $dateDebut = false;
    private $validation = false;
    private $delai = false;

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($x)
    {
        $this->position = $x;

        return $this;
    }

    public function getNbrJEH()
    {
        return $this->nbrJEH;
    }

    public function getPrixJEH()
    {
        return $this->prixJEH;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getObjectif()
    {
        return $this->objectif;
    }

    public function getMethodo()
    {
        return $this->methodo;
    }

    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    public function getValidation()
    {
        return $this->validation;
    }

    public function getDelai()
    {
        return $this->delai;
    }

    public function setNbrJEH($x)
    {
        $this->nbrJEH = $x;

        return $this;
    }

    public function setPrixJEH($x)
    {
        $this->prixJEH = $x;

        return $this;
    }

    public function setTitre($x)
    {
        $this->titre = $x;

        return $this;
    }

    public function setObjectif($x)
    {
        $this->objectif = $x;

        return $this;
    }

    public function setMethodo($x)
    {
        $this->methodo = $x;

        return $this;
    }

    public function setDateDebut($x)
    {
        $this->dateDebut = $x;

        return $this;
    }

    public function setValidation($x)
    {
        $this->validation = $x;

        return $this;
    }

    public function setDelai($x)
    {
        $this->delai = $x;

        return $this;
    }
}

class AvController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Av:index.html.twig', array(
                    'etudes' => $entities,
                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
        return $this->modifierAction(null, $id);
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Av')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('La Convention Cliente n\'existe pas !');
        }

        $etude = $entity->getEtude();

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Av:voir.html.twig', array(
                    'av' => $entity,
                /* 'delete_form' => $deleteForm->createView(),  */));
    }

    private function getPhaseByPosition($position, $array)
    {
        foreach ($array as $phase) {
            if ($phase->getPosition() == $position) {
                return $phase;
            }
        }

        return;
    }

    public static $phaseMethodes = array('NbrJEH', 'PrixJEH', 'Titre', 'Objectif', 'Methodo', 'DateDebut', 'Validation', 'Delai', 'Position');

    private function mergePhaseIfNotNull($phaseReceptor, $phaseToMerge, $changes)
    {
        foreach (self::$phaseMethodes as $methode) {
            $getMethode = 'get'.$methode;
            $setMethode = 'set'.$methode;
            if ($phaseToMerge->$getMethode() != null) {
                $changes->$setMethode(true);
                $phaseReceptor->$setMethode($phaseToMerge->$getMethode());
            }
        }
    }

    private function copyPhase($source, $destination)
    {
        foreach (self::$phaseMethodes as $methode) {
            $getMethode = 'get'.$methode;
            $setMethode = 'set'.$methode;
            $destination->$setMethode($source->$getMethode());
        }
    }

    private function phaseChange($phase)
    {
        $isNotNull = false;
        foreach (self::$phaseMethodes as $methode) {
            $getMethode = 'get'.$methode;
            $isNotNull = $isNotNull || ($phase->$getMethode() != null && $methode != 'Position');
        }

        return $isNotNull;
    }

    private function nullFielIfEqual($phaseReceptor, $phaseToCompare)
    {
        $isNotNull = false;
        foreach (self::$phaseMethodes as $methode) {
            $getMethode = 'get'.$methode;
            $setMethode = 'set'.$methode;
            if ($phaseReceptor->$getMethode() == $phaseToCompare->$getMethode() && $methode != 'Position') {
                $phaseReceptor->$setMethode(null);
            } else {
                $isNotNull = true;
            }
        }

        return $isNotNull;
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id, $idEtude = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($idEtude) {
            if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($idEtude)) {
                throw $this->createNotFoundException('L\'étude n\'existe pas !');
            }
            $av = new Av();
            $av->setEtude($etude);
            $etude->addAv($av);
        } elseif (!$av = $em->getRepository('mgate\SuiviBundle\Entity\Av')->find($id)) {
            throw $this->createNotFoundException('L\'avenant n\'existe pas !');
        }

        $etude = $av->getEtude();

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        $phasesAv = array();
        if ($av->getPhases()) {
            $phasesAv = $av->getPhases()->toArray();

            foreach ($av->getPhases() as $phase) {
                $av->removePhase($phase);
                $em->remove($phase);
            }
        }

        $phasesChanges = array();

        $phasesEtude = $av->getEtude()->getPhases()->toArray();
        foreach ($phasesEtude as $phase) {
            $changes = new PhaseChange();
            $phaseAV = new \mgate\SuiviBundle\Entity\Phase();

            $this->copyPhase($phase, $phaseAV);

            if ($phaseOriginAV = $this->getPhaseByPosition($phaseAV->getPosition(), $phasesAv)) {
                $this->mergePhaseIfNotNull($phaseAV, $phaseOriginAV, $changes);
            }

            $phaseAV->setEtude()->setAvenant($av);
            $av->addPhase($phaseAV);
            $phasesChanges[] = $changes;
        }

        $form = $this->createForm(new AvType(), $av, array('prospect' => $av->getEtude()->getProspect()));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $phasesEtude = $av->getEtude()->getPhases()->getValues();
                foreach ($av->getPhases() as $phase) {
                    $toKeep = false;
                    $av->removePhase($phase);

                    if (!$phaseEtude = $this->getPhaseByPosition($phase->getPosition(), $phasesEtude)) {
                        $toKeep = true;
                    }

                    if (isset($phaseEtude)) {
                        $toKeep = $this->nullFielIfEqual($phase, $phaseEtude);
                    }

                    if ($toKeep) {
                        $av->addPhase($phase);
                    }

                    unset($phaseEtude);
                }

                foreach ($av->getPhases() as $phase) {
                    $phase->setEtatSurAvenant(0);
                    if ($this->phaseChange($phase)) { // S'il n'y a plus de modification sur la phase
                        $em->persist($phase);
                    } else {
                        $av->removePhase($phase);
                    }
                }

                if ($idEtude) { // Si on ajoute un avenant
                    $em->persist($etude);
                } else { // Si on modifie un avenant
                    $em->persist($av);
                }
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_av_voir', array('id' => $av->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:Av:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'av' => $av,
                    'changes' => $phasesChanges,
                ));
    }
}
