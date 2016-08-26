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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use mgate\SuiviBundle\Entity\AvMission;
use mgate\SuiviBundle\Form\Type\AvMissionHandler;
use mgate\SuiviBundle\Form\Type\AvMissionType;

class AvMissionController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        $avmission = new AvMission();
        $avmission->setEtude($etude);
        $form = $this->createForm(new AvMissionType(), $avmission);
        $formHandler = new AvMissionHandler($form, $this->get('request'), $em);

        if ($formHandler->process()) {
            return $this->redirect($this->generateUrl('mgateSuivi_avmission_voir', array('id' => $avmission->getId())));
        }

        return $this->render('mgateSuiviBundle:AvMission:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:AvMission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvMission entity.');
        }

        $etude = $entity->getEtude();

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:AvMission:voir.html.twig', array(
            'avmission' => $entity,
            /*'delete_form' => $deleteForm->createView(),  */));
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$avmission = $em->getRepository('mgate\SuiviBundle\Entity\AvMission')->find($id)) {
            throw $this->createNotFoundException('AvMission[id='.$id.'] inexistant');
        }

        $etude = $avmission->getEtude();

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(new AvMissionType(), $avmission);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_avmission_voir', array('id' => $avmission->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:AvMission:modifier.html.twig', array(
            'form' => $form->createView(),
            'avmission' => $avmission,
        ));
    }
}
