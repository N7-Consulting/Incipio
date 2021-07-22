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

use App\Entity\Project\ClientContact;
use App\Entity\Project\Etude;
use App\Form\Project\ClientContactHandler;
use App\Form\Project\ClientContactType;
use App\Service\Project\EtudePermissionChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientContactController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_clientcontact_index", path="/suivi/clientcontact/", methods={"GET","HEAD"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(ClientContact::class)->findBy([], ['date' => 'ASC']);

        return $this->render('Project/ClientContact/index.html.twig', [
            'contactsClient' => $entities,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_clientcontact_ajouter", path="/suivi/clientcontact/ajouter/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function add(Request $request, Etude $etude, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $clientcontact = new ClientContact();
        $clientcontact->setEtude($etude);
        $form = $this->createForm(ClientContactType::class, $clientcontact);
        $formHandler = new ClientContactHandler($form, $request, $em);

        if ($formHandler->process()) {
            return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom(), '_fragment' => 'tab7']);
        }

        return $this->render('Project/ClientContact/ajouter.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }

    private function compareDate(ClientContact $a, ClientContact $b)
    {
        if ($a->getDate() == $b->getDate()) {
            return 0;
        } else {
            return ($a->getDate() < $b->getDate()) ? -1 : 1;
        }
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_clientcontact_modifier", path="/suivi/clientcontact/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(Request $request, ClientContact $clientContact, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        $etude = $clientContact->getEtude();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(ClientContactType::class, $clientContact);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Contact client modifié');

                return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom(), '_fragment' => 'tab7']);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }
        $deleteForm = $this->createDeleteForm($clientContact);

        return $this->render('Project/ClientContact/modifier.html.twig', [
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'clientcontact' => $clientContact,
            'etude' => $etude,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_clientcontact_delete", path="/suivi/clientcontact/supprimer/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function delete(ClientContact $contact, Request $request, EtudePermissionChecker $permChecker)
    {
        $form = $this->createDeleteForm($contact);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($permChecker->confidentielRefus($contact->getEtude(), $this->getUser())) {
                throw new AccessDeniedException('Cette étude est confidentielle');
            }

            $em->remove($contact);
            $em->flush();
            $this->addFlash('success', 'Contact client supprimé');
        }

        return $this->redirectToRoute('project_etude_voir', ['nom' => $contact->getEtude()->getNom()]);
    }

    private function createDeleteForm(ClientContact $contact)
    {
        return $this->createFormBuilder(['id' => $contact->getId()])
            ->add('id', HiddenType::class)
            ->getForm();
    }
}
