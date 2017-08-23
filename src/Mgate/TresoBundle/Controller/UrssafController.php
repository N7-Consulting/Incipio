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

use Genemu\Bundle\FormBundle\Form\JQuery\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UrssafController extends Controller
{
    public function indexAction(Request $request, $year = null, $month = null)
    {
        $em = $this->getDoctrine()->getManager();

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('date', DateType::class, ['label' => 'Missions commencées avant le :', 'required' => true, 'widget' => 'single_text', 'data' => date_create(), 'format' => 'dd/MM/yyyy'])
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                return $this->redirect($this->generateUrl('Mgate_treso_urssaf', ['year' => $data['date']->format('Y'),
                    'month' => $data['date']->format('m'),
                ]));
            }
        }

        if ($year === null || $month === null) {
            $date = new \DateTime('now');
        } else {
            $date = new \DateTime();
            $date->setDate($year, $month, 01);
        }

        $RMs = $em->getRepository('MgateSuiviBundle:Mission')->getMissionsBeginBeforeDate($date);

        return $this->render('MgateTresoBundle:Urssaf:index.html.twig', ['form' => $form->createView(), 'RMs' => $RMs]);
    }
}
