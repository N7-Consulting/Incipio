<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\SuiviBundle\Controller;

use Mgate\SuiviBundle\Entity\Etude;
use Mgate\SuiviBundle\Entity\Suivi;
use Mgate\SuiviBundle\Form\Type\SuiviType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SuiviController extends Controller
{
    /**
     * @Security("has_role('ROLE_CA')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MgateSuiviBundle:Suivi')
            ->createQueryBuilder('s')
            ->innerJoin('s.etude', 'e')
            ->where('e.stateID < 5')
            //->groupBy('s.date')
            ->orderBy('e.mandat', 'DESC')
            ->addOrderBy('e.num', 'DESC')
            ->addOrderBy('s.date', 'DESC')
            ->getQuery()->getResult();

        return $this->render('MgateSuiviBundle:Suivi:index.html.twig', [
            'suivis' => $entities,
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     *
     * @param Request $request
     * @param Etude   $etude
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Etude $etude)
    {
        $em = $this->getDoctrine()->getManager();

        $suivi = new Suivi();
        $suivi->setEtude($etude);
        $suivi->setDate(new \DateTime('now'));
        $form = $this->createForm(SuiviType::class, $suivi);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($suivi);
                $em->flush();

                return $this->redirect($this->generateUrl('MgateSuivi_suivi_voir', ['id' => $suivi->getId()]));
            }
        }

        return $this->render('MgateSuiviBundle:Suivi:ajouter.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }

    private function compareDate(Suivi $a, Suivi $b)
    {
        if ($a->getDate() == $b->getDate()) {
            return 0;
        } else {
            return ($a->getDate() < $b->getDate()) ? -1 : 1;
        }
    }

    /**
     * @Security("has_role('ROLE_CA')")
     *
     * @param Suivi $suivi
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirAction(Suivi $suivi)
    {
        $etude = $suivi->getEtude();
        $suivis = $etude->getSuivis()->toArray();
        usort($suivis, [$this, 'compareDate']);

        return $this->render('MgateSuiviBundle:Suivi:voir.html.twig', [
            'suivis' => $suivis,
            'selectedSuivi' => $suivi,
            'etude' => $etude,
            ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     */
    public function modifierAction(Request $request, Suivi $suivi)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(SuiviType::class, $suivi);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();

                return $this->redirect($this->generateUrl('MgateSuivi_suivi_voir', ['id' => $suivi->getId()]));
            }
        }

        return $this->render('MgateSuiviBundle:Suivi:modifier.html.twig', [
            'form' => $form->createView(),
            'clientcontact' => $suivi,
            'etude' => $suivi->getEtude(),
        ]);
    }
}
