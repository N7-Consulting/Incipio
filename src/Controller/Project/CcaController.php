<?php

namespace App\Controller\Project;

use App\Entity\Project\Cca;
use App\Entity\Project\Bdc;
use App\Form\Project\CcaType;
use App\Form\Project\SubCcaType;
use DateTime;
use App\Service\Project\DocTypeManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CcaController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route("/suivi/cca", name="project_cca_voir")
     *
     * @return RedirectResponse|Response
     */
    public function voir(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $ccas = $em->getRepository(Cca::class)->findAll();
        // On veut récupérer leur nombre pour l'afficher entre parenthèses
        $bdcs = $em->getRepository(Bdc::class)->findAll();

        $nbBdc = [];
        foreach ($ccas as $cca) {
            $nb = 0;
            foreach ($bdcs as $bdc) {
                if ($bdc->getEtude()->getCca() === $cca) {
                    ++$nb;
                    // L'efficacité de cette méthode étant terrible (peut-être cela pouvait-il se résoudre avec de meilleures requêtes SQL...) on l'améliore un peu en réduisant la taille de l'array $bdcs
                    $key = array_search($bdc, $bdcs);
                    unset($bdcs[$key]);
                }
            }
            $nbBdc[] = $nb;
        }
        return $this->render('Project/Cca/voir.html.twig', [
            'controller_name' => 'CcaController',
            'ccas' => $ccas,
            'nbBdc' => $nbBdc,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route("/suivi/cca/bdc/{id}", name="project_cca_bdc")
     *
     * @return RedirectResponse|Response
     */
    public function voirBdc(Cca $cca): Response
    {
        $em = $this->getDoctrine()->getManager();
        $nomProspect = $cca->getProspect()->getNom();
        $bdcs = $em->getRepository(Bdc::class)->findAllByCca($cca->getId());
        return $this->render('Project/Cca/bdcs.html.twig', [
            'controller_name' => 'CcaController',
            'bdcs' => $bdcs,
            'prospect' => $nomProspect
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     * @Route("/suivi/cca/delete/{id}", name="project_cca_supprimer")
     *
     * @return RedirectResponse
     */
    public function supprimer(Cca $cca, Request $request): Response
    {
        $form = $this->createDeleteForm($cca);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $nomProspect = $cca->getProspect()->getNom();
            $em->remove($cca);
            $em->flush($cca);

            $this->addFlash('success', 'Convention Cadre Agile avec ' . $nomProspect . ' bien supprimée');
        }

        return $this->redirectToRoute('project_cca_voir');
    }

    private function createDeleteForm(Cca $cca)
    {
        return $this->createFormBuilder(['id' => $cca->getId()])
            ->add('id', HiddenType::class)
            ->getForm();
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route("/suivi/cca/add", name="project_cca_ajouter")
     *
     * @return RedirectResponse|Response
     */
    public function add(Request $request): Response
    {
        $cca = new Cca();

        // We need to have a prospect in order to include DocTypeType Form without issues so we devide the form in two pieces.
        // First we ask for internal name and prospect
        // Then we go to modifier method.
        $form = $this->createForm(CcaType::class, $cca);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $cca = $form->getData();

                // Javascript to add a new prospect dynamically
                if ((!$cca->isKnownProspect() && !$cca->getNewProspect()) || ($cca->isKnownProspect() && !$cca->getProspect())) {
                    $this->addFlash('danger', 'Vous devez définir un prospect');

                    return $this->render('Project/Cca/ajouter.html.twig', [
                        'form' => $form->createView(),
                    ]);

                } elseif (!$cca->isKnownProspect()) {
                    $cca->setProspect($cca->getNewProspect());
                }

                $dateFinEstimee = new DateTime(date('Y-m-d', strtotime('+1 year')));
                $cca->setDateFin($dateFinEstimee);

                $em = $this->getDoctrine()->getManager();
                $em->persist($cca);
                $em->flush();

                return $this->redirectToRoute('project_cca_modifier', ['id' => $cca->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Project/Cca/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route("/suivi/cca/modify/{id}", name="project_cca_modifier")
     *
     * @return RedirectResponse|Response
     */
    public function modifier(Request $request, Cca $cca, DocTypeManager $docTypeManager): Response
    {
        if(!$cca->getVersion())
            $cca->setVersion(1);

        $form = $this->createForm(SubCcaType::class, $cca, ['prospect' => $cca->getProspect()]);
        $deleteForm = $this->createDeleteForm($cca);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $cca = $form->getData();
                $em = $this->getDoctrine()->getManager();

                // Save signataire is unknown
                $docTypeManager->checkSaveNewEmploye($cca);
                $em->flush();

                return $this->redirectToRoute('project_cca_voir');
            }
        }

        return $this->render('Project/Cca/modifier.html.twig', [
            'form' => $form->createView(),
            'cca' => $cca,
            'delete_form' => $deleteForm->createView(),
        ]);
    }
}
