<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Formation;

use App\Entity\Formation\Formation;
use App\Entity\Formation\Passation;
use App\Form\Formation\FormationType;
use App\Form\Formation\PassationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Publish\DocumentController;
use Symfony\Component\HttpKernel\KernelInterface;

class FormationController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="formations_index_admin", path="/Formations/Admin", methods={"GET","HEAD"})
     *
     * Display a list of all training given order by date desc
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $formationsParMandat = $em->getRepository(Formation::class)->findAllByMandat();

        return $this->render('Formation/Formations/index.html.twig', [
            'formationsParMandat' => $formationsParMandat,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="passation_lister", path="/Passations", methods={"GET","HEAD"})
     *
     * Display a list of all training group by term.
     */
    public function lister()
    {   
        $em = $this->getDoctrine()->getManager();
        $passationGestionAsso = $em->getRepository(Passation::class)->findBy(['categorie' => 1]);
        $passationRFP = $em->getRepository(Passation::class)->findBy(['categorie' => 2]);
        $passationGestionEtude = $em->getRepository(Passation::class)->findBy(['categorie' => 3]);
        $passationTreso = $em->getRepository(Passation::class)->findBy(['categorie' => 4]);
        $passationDevCo = $em->getRepository(Passation::class)->findBy(['categorie' => 5]);
        $passationQualite = $em->getRepository(Passation::class)->findBy(['categorie' => 6]);
        $passationAutre = $em->getRepository(Passation::class)->findBy(['categorie' => 7]);
        return $this->render('Formation/Passations/lister.html.twig', [
            'passationGestionAsso' => $passationGestionAsso,
            'passationRFP' => $passationRFP,
            'passationGestionEtude' => $passationGestionEtude,
            'passationTreso' => $passationTreso,
            'passationDevCo' => $passationDevCo,
            'passationQualite' => $passationQualite,
            'passationAutre' => $passationAutre,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="formation_voir", path="/Formations/{id}", methods={"GET","HEAD","POST"}, requirements={"id": "\d+"})
     *
     * @param Formation $formation The training to display
     *
     * @return Response
     */
    public function voir(Formation $formation)
    {
        return $this->render('Formation/Formations/voir.html.twig', [
            'formation' => $formation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="passation_voir", path="/Passation/{id}", methods={"GET","HEAD","POST"}, requirements={"id": "\d+"})
     *
     * @param Passation $formation The training to display
     *
     * @return Response
     */
    public function voirPassation(Passation $passation)
    {
        return $this->render('Formation/Passations/voir.html.twig', [
            'passation' => $passation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="formation_ajouter", path="/Formations/Admin/Ajouter", methods={"GET","HEAD","POST"})
     *
     * @return Response
     */
    public function ajouter(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($formation);
                $em->flush();
                $this->addFlash('success', 'Formation enregistrée');

                return $this->redirectToRoute('formation_voir', ['id' => $formation->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Formation/Formations/ajouter.html.twig', ['form' => $form->createView(),
                                                                                'formation' => $formation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="passation_ajouter", path="/Passation/Admin/Ajouter", methods={"GET","HEAD","POST"})
     *
     * @return Response
     */
    public function ajouterPassation(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $passation = new Passation();
        $form = $this->createForm(PassationType::class, $passation);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($passation);
                $em->flush();
                $this->addFlash('success', 'Passation enregistrée');

                return $this->redirectToRoute('passation_voir', ['id' => $passation->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Formation/Passations/ajouter.html.twig', ['form' => $form->createView(),
                                                                                'passation' => $passation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     * @Route(name="formation_modifier", path="/Formations/Admin/Modifier/{id}", methods={"GET","HEAD","POST"}, requirements={"id": "\d+"})
     *
     * @param Formation $formation The training to modify
     *
     * @return Response
     */
    public function modifier(Request $request, Formation $formation)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(FormationType::class, $formation);
        $deleteForm = $this->createDeleteForm($formation->getId());

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($formation);
                $em->flush();
                $this->addFlash('success', 'Formation enregistrée');

                return $this->redirectToRoute('formation_voir', ['id' => $formation->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Formation/Formations/modifier.html.twig', [
            'delete_form' => $deleteForm->createView(),
            'form' => $form->createView(),
            'formation' => $formation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     * @Route(name="passation_modifier", path="/Passation/Admin/Modifier/{id}", methods={"GET","HEAD","POST"}, requirements={"id": "\d+"})
     *
     * @param Passation $formation The training to modify
     *
     * @return Response
     */
    public function modifierPassation(Request $request, Passation $passation)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PassationType::class, $passation);
        $deleteForm = $this->createDeleteForm($passation->getId());

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($passation);
                $em->flush();
                $this->addFlash('success', 'Passation enregistrée');

                return $this->redirectToRoute('passation_voir', ['id' => $passation->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Formation/Passations/modifier.html.twig', [
            'delete_form' => $deleteForm->createView(),
            'form' => $form->createView(),
            'passation' => $passation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     * @Route(name="formation_participation", path="/Formations/Admin/Participation/{mandat}", methods={"GET","HEAD"}, defaults={"mandat": ""})
     *
     * @param $mandat string The mandat during which trainings were given
     *
     * @return Response Manage participant present to a training
     */
    public function participation($mandat = null)
    {
        $em = $this->getDoctrine()->getManager();
        $formationsParMandat = $em->getRepository(Formation::class)->findAllByMandat();

        $choices = [];
        foreach ($formationsParMandat as $key => $value) {
            $choices[$key] = $key;
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'mandat',
                ChoiceType::class,
                [
                    'label' => 'Présents aux formations du mandat ',
                    'choices' => $choices,
                    'required' => true,
                ]
            )->getForm();

        if (null !== $mandat) {
            $formations = array_key_exists($mandat, $formationsParMandat) ? $formationsParMandat[$mandat] : [];
        } else {
            $formations = count($formationsParMandat) ? reset($formationsParMandat) : [];
        }

        $presents = [];

        foreach ($formations as $formation) {
            foreach ($formation->getMembresPresents() as $present) {
                $id = $present->getPrenomNom();
                if (array_key_exists($id, $presents)) {
                    $presents[$id][] = $formation->getId();
                } else {
                    $presents[$id] = [$formation->getId()];
                }
            }
        }

        return $this->render('Formation/Formations/participation.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations,
            'presents' => $presents,
            'mandat' => $mandat,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(name="formation_supprimer", path="/Formations/Admin/Supprimer/{id}", methods={"HEAD","POST"})
     *
     * @param Formation $formation The training to delete (paramconverter from id)
     *
     * @return RedirectResponse Delete a training
     */
    public function supprimer(Request $request, Formation $formation, KernelInterface $kernel)
    {
        $form = $this->createDeleteForm($formation->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $docs = $formation->getRelatedDocuments();
            foreach ($docs as $document) {
                $doc = $document->getDocument();
                DocumentController::deleteRelated($em,$doc,$kernel);
            }
            $em->remove($formation);
            $em->flush();
            $this->addFlash('success', 'Formation supprimée');
        }

        return $this->redirectToRoute('formations_index_admin');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(name="passation_supprimer", path="/Passations/Admin/Supprimer/{id}", methods={"HEAD","POST"})
     *
     * @param Passation $passation The training to delete (paramconverter from id)
     *
     * @return RedirectResponse Delete a training
     */
    public function supprimerPassation(Request $request, Passation $passation, KernelInterface $kernel)
    {
        $form = $this->createDeleteForm($passation->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $docs = $passation->getRelatedDocuments();
            foreach ($docs as $document) {
                $doc = $document->getDocument();
                DocumentController::deleteRelated($em,$doc,$kernel);
            }
            $em->remove($passation);
            $em->flush();
            $this->addFlash('success', 'Passation supprimée');
        }

        return $this->redirectToRoute('passation_lister', []);
    }

    /**
     * Function to create a form to remove a formation.
     *
     * @param $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(['id' => $id])
            ->add('id', HiddenType::class)
            ->getForm();
    }
}
