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
use mgate\TresoBundle\Entity\BaseURSSAF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use mgate\TresoBundle\Form\BaseURSSAFType;

class BaseURSSAFController extends Controller
{
    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bases = $em->getRepository('mgateTresoBundle:BaseURSSAF')->findAll();

        return $this->render('mgateTresoBundle:BaseURSSAF:index.html.twig', array('bases' => $bases));
    }

    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$base = $em->getRepository('mgateTresoBundle:BaseURSSAF')->find($id)) {
            $base = new BaseURSSAF();
        }

        $form = $this->createForm(new BaseURSSAFType(), $base);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));
            if ($form->isValid()) {
                $em->persist($base);
                $em->flush();

                return $this->redirect($this->generateUrl('mgateTreso_BaseURSSAF_index', array()));
            }
        }

        return $this->render('mgateTresoBundle:BaseURSSAF:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'base' => $base,
                ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$base = $em->getRepository('mgateTresoBundle:BaseURSSAF')->find($id)) {
            throw $this->createNotFoundException('La base URSSAF n\'existe pas !');
        }

        $em->remove($base);
        $em->flush();

        return $this->redirect($this->generateUrl('mgateTreso_BaseURSSAF_index', array()));
    }
}
