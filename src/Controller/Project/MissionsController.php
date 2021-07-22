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
use App\Form\Project\MissionsType;
use App\Service\Project\EtudePermissionChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class MissionsController extends AbstractController
{

    public $keyValueStore;

    public function __construct(KeyValueStore $keyValueStore)
    {
        $this->keyValueStore = $keyValueStore;
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_missions_modifier", path="/suivi/missions/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(Request $request, Etude $etude, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        /* Form handling */

        $pourcentageAcompte = $this->keyValueStore->get('pourcentageAcompteDefaut');

        $form = $this->createForm(MissionsType::class, $etude, ['etude' => $etude, 'acompte' => $pourcentageAcompte]);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                foreach ($form->get('missions') as $missionForm) {
                    $m = $missionForm->getData();
                    foreach ($missionForm->get('repartitionsJEH') as $repartitionForm) {
                        $r = $repartitionForm->getData();
                        /* @var RepartitionJEH $r */
                        $r->setMission($m);
                    }
                    /* @var Mission $m */
                    $m->setEtude($etude);
                }
                $em->persist($etude);
                $em->flush();
                $this->addFlash('success', 'Mission enregistrée');

                return $this->redirectToRoute('project_missions_modifier', ['id' => $etude->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Project/Mission/missions.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }
}
