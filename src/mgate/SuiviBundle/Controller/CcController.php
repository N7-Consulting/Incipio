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
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Entity\Cc;
use mgate\SuiviBundle\Form\CcType;

class CcController extends Controller
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

        $entity = $em->getRepository('mgateSuiviBundle:Cc')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('La CC n\'existe pas !');
        }

        $etude = $entity->getEtude();

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Cc:voir.html.twig', array(
            'cc' => $entity,
            /*'delete_form' => $deleteForm->createView(),  */));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        }

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle');
        }

        if (!$cc = $etude->getCc()) {
            $cc = new Cc();
            $etude->setCc($cc);
        }

        $form = $this->createForm(new CcType(), $etude, array('prospect' => $etude->getProspect()));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $this->get('mgate.doctype_manager')->checkSaveNewEmploye($etude->getCc());
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('nom' => $etude->getNom())));
            }
        }

        return $this->render('mgateSuiviBundle:Cc:rediger.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
}
