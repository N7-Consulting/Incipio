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
use App\Entity\Personne\Poste;
use App\Form\Personne\PosteType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PosteController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="personne_poste_ajouter", path="/poste/add", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function ajouter(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $poste = new Poste();

        $form = $this->createForm(PosteType::class, $poste);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($poste);
                $em->flush();
                $this->addFlash('success', 'Poste ajouté');

                return $this->redirectToRoute('personne_poste_homepage');
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Personne/Poste/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="personne_poste_homepage", path="/poste", methods={"GET","HEAD","POST"})
     *
     * @return Response
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $postes = $em->getRepository(Poste::class)->findAll();
        $filieres = $em->getRepository(Filiere::class)->findAll();
        

        return $this->render('Personne/Poste/index.html.twig', [
            'postes' => $postes,
            'filieres' => $filieres,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="personne_poste_modifier", path="/poste/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     *
     * @internal param $id
     */
    public function modifier(Request $request, Poste $poste, ObjectManager $em)
    {
        // On passe l'$article récupéré au formulaire
        $form = $this->createForm(PosteType::class, $poste);
        $deleteForm = $this->createDeleteForm($poste->getId());
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($poste);
                $em->flush();
                $this->addFlash('success', 'Poste modifié');

                return $this->redirectToRoute('personne_poste_homepage');
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Personne/Poste/modifier.html.twig', [
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="personne_poste_supprimer", path="/poste/supprimer/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Poste $poste, ObjectManager $em)
    {
        $form = $this->createDeleteForm($poste->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (0 == $poste->getMandats()->count()) { //collection contains no mandats
                foreach ($poste->getMandats() as $membre) {
                    $membre->setPoste(null);
                }
                $em->remove($poste);
                $em->flush();
                $this->addFlash('success', 'Poste supprimé');

                return $this->redirectToRoute('personne_poste_homepage');
            }
            $this->addFlash('danger', 'Impossible de supprimer un poste ayant des membres.');
        }

        return $this->redirectToRoute('personne_poste_modifier', ['id' => $poste->getId()]);
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(['id' => $id])
            ->add('id', HiddenType::class)
            ->getForm();
    }
}
