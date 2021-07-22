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
use App\Entity\Project\GroupePhases;
use App\Form\Project\GroupesPhasesType;
use App\Service\Project\EtudePermissionChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GroupePhasesController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_groupes_modifier", path="/suivi/groupes/modifier/{id}", methods={"GET","HEAD","POST"}, requirements={"id": "\d+"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(Request $request, Etude $etude, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(GroupesPhasesType::class, $etude);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($request->get('add')) {
                    $groupeNew = new GroupePhases();
                    $groupeNew->setNumero(count($etude->getGroupes()));
                    $groupeNew->setEtude($etude);
                    $groupeNew->setTitre('');
                    $groupeNew->setDescription('');
                    $etude->addGroupe($groupeNew);
                    $message = 'Groupe ajouté';
                }

                $em->persist($etude); // persist $etude / $form->getData()
                $em->flush();
                $this->addFlash('success', isset($message) ? $message : 'Groupes modifiés');

                return $this->redirectToRoute('project_groupes_modifier', ['id' => $etude->getId()]);
            }

            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Project/GroupePhases/modifier.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }
}
