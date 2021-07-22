<?php

namespace App\Controller\Project;

use App\Entity\Project\Bdc;
use App\Entity\Project\Etude;
use App\Form\Project\BdcType;
use App\Service\Project\DocTypeManager;
use App\Service\Project\EtudePermissionChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BdcController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_bdc_rediger", path="/suivi/bdc/rediger/{id}", methods={"GET","HEAD","POST"})
     *
     * @param Etude $etude etude which BDC should belong to
     *
     * @return RedirectResponse|Response
     */
    public function rediger(Request $request, Etude $etude, EtudePermissionChecker $permChecker, DocTypeManager $docTypeManager)
    {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        if (!$bdc = $etude->getBdc()) {
            $bdc = new Bdc();
            $etude->setBdc($bdc);
        }

        $form = $this->createForm(BdcType::class, $etude, ['prospect' => $etude->getProspect()]);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $docTypeManager->checkSaveNewEmploye($etude->getBdc());
                $em->flush();

                $this->addFlash('success', 'Bon de Commande modifié');
                if ($request->get('phases')) {
                    return $this->redirectToRoute('project_phases_modifier', ['id' => $etude->getId()]);
                }

                return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom(), '_fragment' => 'tab3']);
            }
        }

        return $this->render('Project/Bdc/rediger.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }
}
