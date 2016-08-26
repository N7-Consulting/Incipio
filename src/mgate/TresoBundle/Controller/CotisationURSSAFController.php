<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\TresoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use mgate\TresoBundle\Entity\CotisationURSSAF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use mgate\TresoBundle\Form\CotisationURSSAFType;

class CotisationURSSAFController extends Controller
{
    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cotisations = $em->getRepository('mgateTresoBundle:CotisationURSSAF')->findAll();

        return $this->render('mgateTresoBundle:CotisationURSSAF:index.html.twig', array('cotisations' => $cotisations));
    }

    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$cotisation = $em->getRepository('mgateTresoBundle:CotisationURSSAF')->find($id)) {
            $cotisation = new CotisationURSSAF();
        }

        $form = $this->createForm(new CotisationURSSAFType(), $cotisation);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));
            if ($form->isValid()) {
                $em->persist($cotisation);
                $em->flush();

                return $this->redirect($this->generateUrl('mgateTreso_CotisationURSSAF_index', array()));
            }
        }

        return $this->render('mgateTresoBundle:CotisationURSSAF:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'cotisation' => $cotisation,
                ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$cotisation = $em->getRepository('mgateTresoBundle:CotisationURSSAF')->find($id)) {
            throw $this->createNotFoundException('La Cotisation URSSAF n\'existe pas !');
        }

        $em->remove($cotisation);
        $em->flush();

        return $this->redirect($this->generateUrl('mgateTreso_CotisationURSSAF_index', array()));
    }
}
