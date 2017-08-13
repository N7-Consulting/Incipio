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

use Genemu\Bundle\FormBundle\Form\JQuery\Type\DateType as GenemuDateType;
use Mgate\TresoBundle\Entity\Facture;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;

class DeclaratifController extends Controller
{
    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bvs = $em->getRepository('MgateTresoBundle:BV')->findAll();

        return $this->render('MgateTresoBundle:BV:index.html.twig', ['bvs' => $bvs]);
    }

    /**
     * @Security("has_role('ROLE_TRESO')")
     */
    public function tvaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = [];
        $tvaCollectee = [];
        $tvaDeductible = [];
        $totalTvaCollectee = ['HT' => 0, 'TTC' => 0, 'TVA' => 0];
        $totalTvaDeductible = ['HT' => 0, 'TTC' => 0, 'TVA' => 0];
        $tvas = [];

        $defaultData = ['message' => 'Date'];
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'date', GenemuDateType::class,
                [
                    'label' => 'Mois considéré',
                    'required' => true, 'widget' => 'single_text',
                    'data' => date_create(), 'format' => 'dd/MM/yyyy', ])
            ->add('trimestriel', CheckboxType::class, ['label' => 'Trimestriel ?', 'required' => false])
            ->getForm();

        $nfs = [];
        $fas = [];
        $fvs = [];

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $data = $form->getData();
            $date = $data['date'];
            $month = $date->format('m');
            $year = $date->format('Y');
        } else {
            $date = new \DateTime('now');
            $month = $date->format('m');
            $year = $date->format('Y');
        }
        setlocale(LC_TIME, 'fra_fra');
        if (array_key_exists('trimestriel', $data) && $data['trimestriel']) {
            $periode = 'Déclaratif pour la période : ' . utf8_encode(strftime('%B', $date->format('U')) . ' - ' . strftime('%B', $date->modify('+2 month')->format('U')));
            for ($i = 0; $i < 3; ++$i) {
                $nfs = $em->getRepository('MgateTresoBundle:NoteDeFrais')->findAllByMonth($month, $year, true);
                $fas = $em->getRepository('MgateTresoBundle:Facture')->findAllTVAByMonth(Facture::TYPE_ACHAT, $month, $year, true);
                $fvs = $em->getRepository('MgateTresoBundle:Facture')->findAllTVAByMonth(Facture::TYPE_VENTE, $month, $year, true);
            }
        } else {
            $periode = 'Déclaratif pour la période : ' . utf8_encode(strftime('%B', $date->format('U')));
            $nfs = $em->getRepository('MgateTresoBundle:NoteDeFrais')->findAllByMonth($month, $year);
            $fas = $em->getRepository('MgateTresoBundle:Facture')->findAllTVAByMonth(Facture::TYPE_ACHAT, $month, $year);
            $fvs = $em->getRepository('MgateTresoBundle:Facture')->findAllTVAByMonth(Facture::TYPE_VENTE, $month, $year);
        }

        /*
         * TVA DEDUCTIBLE
         */
        foreach ([$fas, $nfs] as $entityDeductibles) {
            foreach ($entityDeductibles as $entityDeductible) {
                $montantTvaParType = [];
                $montantHT = 0;
                $montantTTC = 0;
                foreach ($entityDeductible->getDetails() as $entityDeductibled) {
                    $tauxTVA = $entityDeductibled->getTauxTVA();
                    if (key_exists($tauxTVA, $montantTvaParType)) {
                        $montantTvaParType[$tauxTVA] += $entityDeductibled->getMontantTVA();
                    } else {
                        $montantTvaParType[$tauxTVA] = $entityDeductibled->getMontantTVA();
                    }
                    $montantHT += $entityDeductibled->getMontantHT();
                    $montantTTC += $entityDeductibled->getMontantTTC();

                    // mise à jour des montant Globaux
                    $totalTvaDeductible['HT'] += $entityDeductibled->getMontantHT();
                    $totalTvaDeductible['TTC'] += $entityDeductibled->getMontantTTC();
                    $totalTvaDeductible['TVA'] += $entityDeductibled->getMontantTVA();

                    // Mise à jour du montant global pour le taux de TVA ciblé
                    if (!in_array($tauxTVA, $tvas) && $tauxTVA !== null) {
                        $tvas[] = $tauxTVA;
                    }
                    if (!key_exists($tauxTVA, $totalTvaDeductible)) {
                        $totalTvaDeductible[$tauxTVA] = $entityDeductibled->getMontantTVA();
                    } else {
                        $totalTvaDeductible[$tauxTVA] += $entityDeductibled->getMontantTVA();
                    }
                }
                $tvaDeductible[] = ['DATE' => $entityDeductible->getDate(), 'LI' => $entityDeductible->getReference(), 'HT' => $montantHT, 'TTC' => $montantTTC, 'TVA' => $entityDeductible->getMontantTVA(), 'TVAT' => $montantTvaParType];
            }
        }

        /*
         * TVA COLLECTE
         */
        foreach ($fvs as $fv) {
            $montantTvaParType = [];

            $montantHT = $fv->getMontantHT();
            $montantTTC = $fv->getMontantTVA();

            // Mise à jour du montant global pour le taux de TVA ciblé
            $totalTvaCollectee['HT'] += $fv->getMontantHT();
            $totalTvaCollectee['TTC'] += $fv->getMontantTTC();
            $totalTvaCollectee['TVA'] += $fv->getMontantTVA();

            foreach ($fv->getDetails() as $fvd) {
                $tauxTVA = $fvd->getTauxTVA();
                if (key_exists($tauxTVA, $montantTvaParType)) {
                    $montantTvaParType[$tauxTVA] += $fvd->getMontantTVA();
                } else {
                    $montantTvaParType[$tauxTVA] = $fvd->getMontantTVA();
                }

                if (!key_exists($tauxTVA, $totalTvaCollectee)) {
                    $totalTvaCollectee[$tauxTVA] = $fvd->getMontantTVA();
                } else {
                    $totalTvaCollectee[$tauxTVA] += $fvd->getMontantTVA();
                }

                // Ajout de l'éventuel nouveau taux de TVA à la liste des taux
                if (!in_array($tauxTVA, $tvas) && $tauxTVA !== null) {
                    $tvas[] = $tauxTVA;
                }
            }
            if ($md = $fv->getMontantADeduire()) {
                $tauxTVA = $md->getTauxTVA();
                if (key_exists($tauxTVA, $montantTvaParType)) {
                    $montantTvaParType[$tauxTVA] -= $md->getMontantTVA();
                } else {
                    $montantTvaParType[$tauxTVA] = -$md->getMontantTVA();
                }

                if (!key_exists($tauxTVA, $totalTvaCollectee)) {
                    $totalTvaCollectee[$tauxTVA] = -$md->getMontantTVA();
                } else {
                    $totalTvaCollectee[$tauxTVA] -= $md->getMontantTVA();
                }

                // Ajout de l'éventuel nouveau taux de TVA à la liste des taux
                if (!in_array($tauxTVA, $tvas) && $tauxTVA !== null) {
                    $tvas[] = $tauxTVA;
                }
            }

            $tvaCollectee[] = ['DATE' => $fv->getDate(), 'LI' => $fv->getReference(), 'HT' => $montantHT, 'TTC' => $montantTTC, 'TVA' => $fv->getMontantTVA(), 'TVAT' => $montantTvaParType];
        }
        sort($tvas);

        return $this->render('MgateTresoBundle:Declaratif:TVA.html.twig',
            ['form' => $form->createView(),
                'tvas' => $tvas,
                'tvaDeductible' => $tvaDeductible,
                'tvaCollectee' => $tvaCollectee,
                'totalTvaDeductible' => $totalTvaDeductible,
                'totalTvaCollectee' => $totalTvaCollectee,
                'periode' => $periode,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_TRESO')")
     *
     * @param Request $request
     * @param null    $year
     * @param null    $month
     */
    public function brcAction(Request $request, $year, $month)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder(['message' => 'Date'])
            ->add(
                'date', GenemuDateType::class,
                [
                    'label' => 'Mois du déclaratif',
                    'required' => true, 'widget' => 'single_text',
                    'data' => date_create(), 'format' => 'dd/MM/yyyy', ]
            )->getForm();

        if ($request->isMethod('POST')) {
            //small hack to keep api working
            $form->handleRequest($request);
            $data = $form->getData();
            $date = $data['date'];

            return $this->redirect($this->generateUrl('MgateTreso_Declaratif_BRC', ['year' => $date->format('Y'),
                                                                                        'month' => $date->format('m'),
                ]));
        }

        if ($year === null || $month === null) {
            $date = new \DateTime('now');
            $month = $date->format('m');
            $year = $date->format('Y');
        }

        $bvs = $em->getRepository('MgateTresoBundle:BV')->findAllByMonth($month, $year);

        $salarieRemunere = [];
        foreach ($bvs as $bv) {
            $id = $bv->getMission()->getIntervenant()->getIdentifiant();
            $salarieRemunere[$id] = 1;
        }

        return $this->render('MgateTresoBundle:Declaratif:BRC.html.twig',
            ['form' => $form->createView(), 'bvs' => $bvs, 'nbSalarieRemunere' => count($salarieRemunere)]
        );
    }
}
