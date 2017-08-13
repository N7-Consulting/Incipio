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

use Mgate\SuiviBundle\Entity\Phase;
use Mgate\SuiviBundle\Form\Type\PhasesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PhasesController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MgateSuiviBundle:Etude')->findAll();

        return $this->render('MgateSuiviBundle:Etude:index.html.twig', [
            'etudes' => $entities,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function modifierAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('Mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        }

        if ($this->get('Mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $originalPhases = [];
        // Create an array of the current Phase objects in the database
        foreach ($etude->getPhases() as $phase) {
            $originalPhases[] = $phase;
        }

        $form = $this->createForm(PhasesType::class, $etude, ['etude' => $etude]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($request->get('add')) {
                    $phaseNew = new Phase();
                    $phaseNew->setPosition(count($etude->getPhases()));
                    $phaseNew->setEtude($etude);
                    $etude->addPhase($phaseNew);
                }

                // filter $originalPhases to contain phases no longer present
                foreach ($etude->getPhases() as $phase) {
                    foreach ($originalPhases as $key => $toDel) {
                        if ($toDel->getId() === $phase->getId()) {
                            unset($originalPhases[$key]);
                        }
                    }
                }

                // remove the relationship between the phase and the etude
                foreach ($originalPhases as $phase) {
                    $em->remove($phase); // on peut faire un persist sinon, cf doc collection form
                }

                $em->persist($etude); // persist $etude / $form->getData()
                $em->flush();
            }

            return $this->redirect($this->generateUrl('MgateSuivi_phases_modifier', ['id' => $etude->getId()]));
        }

        return $this->render('MgateSuiviBundle:Phase:phases.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }
}
