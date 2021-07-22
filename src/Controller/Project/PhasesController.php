<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Project;

use App\Entity\Project\Etude;
use App\Entity\Project\Phase;
use App\Form\Project\PhasesType;
use App\Service\Project\EtudePermissionChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PhasesController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_phases_modifier", path="/suivi/phases/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(Request $request, Etude $etude, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(PhasesType::class, $etude, ['etude' => $etude]);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($request->get('add')) {
                    $phaseNew = new Phase();
                    $phaseNew->setPosition(count($etude->getPhases()));
                    $phaseNew->setEtude($etude);

                    // Add some trivial info based on previous input
                    $phases = array_values($etude->getPhases()->getValues());
                    if (!empty($phases)) {
                        $phasePrevious = end($phases);

                        // ? Préremplissage
                        $phaseNew->setPrixJEH($phasePrevious->getPrixJEH());
                        $phaseNew->setDateDebut($phasePrevious->getDateFin());
                    }
                    $etude->addPhase($phaseNew);
                }

                $em->persist($etude); // persist $etude / $form->getData()
                $em->flush();
                $this->addFlash('success', 'Phases enregistrées');

                return $this->redirectToRoute('project_phases_modifier', ['id' => $etude->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Project/Phase/phases.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }
}
