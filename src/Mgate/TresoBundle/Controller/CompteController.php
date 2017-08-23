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

use Mgate\TresoBundle\Entity\Compte;
use Mgate\TresoBundle\Form\Type\CompteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompteController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $comptes = $em->getRepository('MgateTresoBundle:Compte')->findAll();

        return $this->render('MgateTresoBundle:Compte:index.html.twig', ['comptes' => $comptes]);
    }

    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function modifierAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$compte = $em->getRepository('MgateTresoBundle:Compte')->find($id)) {
            $compte = new Compte();
        }

        $form = $this->createForm(CompteType::class, $compte);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($compte);
                $em->flush();

                return $this->redirect($this->generateUrl('MgateTreso_Compte_index', []));
            }
        }

        return $this->render('MgateTresoBundle:Compte:modifier.html.twig', [
                    'form' => $form->createView(),
                    'compte' => $compte,
                ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$compte = $em->getRepository('MgateTresoBundle:Compte')->find($id)) {
            throw $this->createNotFoundException('Le Compte n\'existe pas !');
        }

        $em->remove($compte);
        $em->flush();

        return $this->redirect($this->generateUrl('MgateTreso_Compte_index', []));
    }
}
