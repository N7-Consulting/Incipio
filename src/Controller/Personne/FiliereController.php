<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Personne;

use App\Entity\Personne\Filiere;
use App\Entity\Hr\SecteurActivite;
use App\Entity\Personne\Membre;
use App\Form\Personne\FiliereType;
use App\Form\Hr\SecteurActiviteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FiliereController extends AbstractController
{
    /**
     * @Route(name="secteur_activite_ajouter", path="/secteur/activite/add", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function ajouterSecteur(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $secteur = new SecteurActivite();

        $form = $this->createForm(SecteurActiviteType::class, $secteur);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($secteur);
                $em->flush();
                $this->addFlash('success', 'Secteur ajouté');

                return $this->redirectToRoute('personne_poste_homepage');
            }
        }

        return $this->render('Hr/Alumni/SecteurActivite/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(name="personne_filiere_ajouter", path="/filiere/add", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function ajouter(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filiere = new Filiere();

        $form = $this->createForm(FiliereType::class, $filiere);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($filiere);
                $em->flush();
                $this->addFlash('success', 'Filière ajoutée');

                return $this->redirectToRoute('personne_poste_homepage');
            }
        }

        return $this->render('Personne/Filiere/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(name="personne_filiere_modifier", path="/filiere/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(Request $request, Filiere $filiere)
    {
        $em = $this->getDoctrine()->getManager();

        // On passe l'$article récupéré au formulaire
        $form = $this->createForm(FiliereType::class, $filiere);
        $deleteForm = $this->createDeleteForm($filiere->getId());

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($filiere);
                $em->flush();
                $this->addFlash('success', 'Filière modifiée');

                return $this->redirectToRoute('personne_poste_homepage');
            }
        }

        return $this->render('Personne/Filiere/modifier.html.twig', [
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(name="personne_filiere_supprimer", path="/filiere/supprimer/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Filiere $filiere)
    {
        $form = $this->createDeleteForm($filiere->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (0 == count($em->getRepository(Membre::class)->findByFiliere($filiere))) { //no members uses that filiere
                $em->remove($filiere);
                $em->flush();
                $this->addFlash('success', 'Filière supprimée avec succès');

                return $this->redirectToRoute('personne_poste_homepage');
            }
            $this->addFlash('danger', 'Impossible de supprimer une filière ayant des membres.');
        } else {
            $this->addFlash('danger', 'Formulaire invalide');
        }

        return $this->redirectToRoute('personne_poste_homepage');
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(['id' => $id])
            ->add('id', HiddenType::class)
            ->getForm();
    }
}
