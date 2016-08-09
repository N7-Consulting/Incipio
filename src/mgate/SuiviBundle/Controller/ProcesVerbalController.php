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
use mgate\SuiviBundle\Entity\ProcesVerbal;
use mgate\SuiviBundle\Form\ProcesVerbalType;
use mgate\SuiviBundle\Form\ProcesVerbalSubType;

class ProcesVerbalController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
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
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:ProcesVerbal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProcesVerbal entity.');
        }

        $etude = $entity->getEtude();

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:ProcesVerbal:voir.html.twig', array(
            'procesverbal' => $entity,
            /*'delete_form' => $deleteForm->createView(),  */));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        }

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        $proces = new ProcesVerbal();
        $etude->addPvi($proces);

        $form = $this->createForm(new ProcesVerbalSubType(), $proces, array('type' => 'pvi', 'prospect' => $etude->getProspect(), 'phases' => count($etude->getPhases()->getValues())));
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->persist($proces);
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_procesverbal_voir', array('id' => $proces->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:ProcesVerbal:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id_pv)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$procesverbal = $em->getRepository('mgate\SuiviBundle\Entity\ProcesVerbal')->find($id_pv)) {
            throw $this->createNotFoundException('Le Procès Verbal n\'existe pas !');
        }

        $etude = $procesverbal->getEtude();

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(new ProcesVerbalSubType(), $procesverbal, array('type' => $procesverbal->getType(), 'prospect' => $procesverbal->getEtude()->getProspect(), 'phases' => count($procesverbal->getEtude()->getPhases()->getValues())));
        $deleteForm = $this->createDeleteForm($id_pv);
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->persist($procesverbal);
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_procesverbal_voir', array('id' => $procesverbal->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:ProcesVerbal:modifier.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'etude' => $procesverbal->getEtude(),
            'type' => $procesverbal->getType(),
            'procesverbal' => $procesverbal,
        ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id_etude, $type)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id_etude)) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        }

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        if (!$procesverbal = $etude->getDoc($type)) {
            $procesverbal = new ProcesVerbal();
            if (strtoupper($type) == 'PVR') {
                $etude->setPvr($procesverbal);
            }

            $procesverbal->setType($type);
        }

        $form = $this->createForm(new ProcesVerbalType(), $etude, array('type' => $type, 'prospect' => $etude->getProspect(), 'phases' => count($etude->getPhases()->getValues())));
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->persist($etude);
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_procesverbal_voir', array('id' => $procesverbal->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:ProcesVerbal:rediger.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
            'type' => $type,
        ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function deleteAction($id_pv)
    {
        $form = $this->createDeleteForm($id_pv);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity = $em->getRepository('mgate\SuiviBundle\Entity\ProcesVerbal')->find($id_pv)) {
                throw $this->createNotFoundException('Le Procès Verbal n\'existe pas !');
            }

            $etude = $entity->getEtude();

            if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
                throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('nom' => $etude->getNom())));
    }

    private function createDeleteForm($id_pv)
    {
        return $this->createFormBuilder(array('id' => $id_pv))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
