<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use mgate\PersonneBundle\Entity\Poste;
use mgate\PersonneBundle\Form\PosteType;

class PosteController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function ajouterAction()
    {
        $em = $this->getDoctrine()->getManager();

        $poste = new Poste();

        $form = $this->createForm(new PosteType(), $poste);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->persist($poste);
                $em->flush();

                return $this->redirect($this->generateUrl('mgatePersonne_poste_voir', array('id' => $poste->getId())));
            }
        }

        return $this->render('mgatePersonneBundle:Poste:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Poste')->findAll();

        return $this->render('mgatePersonneBundle:Poste:index.html.twig', array(
            'postes' => $entities,
        ));
    }

    /**
     * @Security("has_role('ROLE_ELEVE')")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Poste')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Le poste demandé n\'existe pas !');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:Poste:voir.html.twig', array(
            'poste' => $entity,
            /*'delete_form' => $deleteForm->createView(),        */));
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$poste = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->find($id)) {
            throw $this->createNotFoundException('Le poste demandé n\'existe pas !');
        }

        // On passe l'$article récupéré au formulaire
        $form = $this->createForm(new PosteType(), $poste);
        $deleteForm = $this->createDeleteForm($id);
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->persist($poste);
                $em->flush();

                return $this->redirect($this->generateUrl('mgatePersonne_poste_voir', array('id' => $poste->getId())));
            }
        }

        return $this->render('mgatePersonneBundle:Poste:modifier.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->find($id)) {
                throw $this->createNotFoundException('Le poste demandé n\'existe pas !');
            }

            foreach ($entity->getMembres() as $membre) {
                $membre->setPoste(null);
            }

            //$entity->setMembres(null);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgatePersonne_poste_homepage'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
