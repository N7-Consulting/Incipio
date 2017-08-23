<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\TresoBundle\Controller;

use Mgate\TresoBundle\Entity\BaseURSSAF;
use Mgate\TresoBundle\Form\Type\BaseURSSAFType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BaseURSSAFController extends Controller
{
    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bases = $em->getRepository('MgateTresoBundle:BaseURSSAF')->findAll();

        return $this->render('MgateTresoBundle:BaseURSSAF:index.html.twig', ['bases' => $bases]);
    }

    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function modifierAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$base = $em->getRepository('MgateTresoBundle:BaseURSSAF')->find($id)) {
            $base = new BaseURSSAF();
        }

        $form = $this->createForm(BaseURSSAFType::class, $base);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($base);
                $em->flush();

                return $this->redirect($this->generateUrl('MgateTreso_BaseURSSAF_index', []));
            }
        }

        return $this->render('MgateTresoBundle:BaseURSSAF:modifier.html.twig', [
                    'form' => $form->createView(),
                    'base' => $base,
                ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$base = $em->getRepository('MgateTresoBundle:BaseURSSAF')->find($id)) {
            throw $this->createNotFoundException('La base URSSAF n\'existe pas !');
        }

        $em->remove($base);
        $em->flush();

        return $this->redirect($this->generateUrl('MgateTreso_BaseURSSAF_index', []));
    }
}
