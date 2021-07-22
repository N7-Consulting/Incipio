<?php

namespace App\Controller\Hr;

use App\Entity\Hr\Competence;
use App\Entity\Personne\Personne;
use App\Entity\Project\Etude;
use App\Form\Hr\CompetenceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Project\EtudePermissionChecker;
use App\Entity\Hr\AlumnusContact;
use App\Form\Hr\AlumnusContactType;
use App\Form\Hr\AlumnusContactHandler;
use App\Entity\Hr\Alumnus;
use App\Form\Hr\AlumnusType;
use App\Form\Hr\AlumnusHandler;


class AlumniController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="gestion_alumni", path="/rh/alumni", methods={"GET","HEAD"})
     */
    public function index()
    {   
        $em = $this->getDoctrine()->getManager();
        $contactsParAlumnus = $em->getRepository(AlumnusContact::class)->findAllByAlumnus();
        $dernierContactsParAlumnus = $em->getRepository(AlumnusContact::class)->findAllByDernierContact();
        $alumni = $em->getRepository(Alumnus::class)->findAll();

        return $this->render('Hr/Alumni/index.html.twig', [
            'contactsParAlumnus' => $contactsParAlumnus,
            'dernierContactsParAlumnus' => $dernierContactsParAlumnus,
            'alumni' => $alumni,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_alumnuscontact_ajouter", path="/rh/alumnuscontact/ajouter", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function add(Request $request, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        $alumnusContact = new AlumnusContact();
        $form = $this->createForm(AlumnusContactType::class, $alumnusContact);
        $formHandler = new AlumnusContactHandler($form, $request, $em);

        if ($formHandler->process()) {
            return $this->redirectToRoute('gestion_alumni', ['_fragment' => 'contact']);
        }

        return $this->render('Hr/Alumni/Contact/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="alumni_contact_modifier", path="/hr/alumnus/contact/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(Request $request, AlumnusContact $alumnusContact, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AlumnusContactType::class, $alumnusContact);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Contact modifié');

                return $this->redirectToRoute('gestion_alumni', ['_fragment' => 'contact']);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }
        $deleteForm = $this->createDeleteForm($alumnusContact);

        return $this->render('Hr/Alumni/Contact/modifier.html.twig', [
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'alumnusContact' => $alumnusContact,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="alumnus_contact_delete", path="/hr/alumnus/contact/supprimer/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function delete(AlumnusContact $contact, Request $request, EtudePermissionChecker $permChecker)
    {
        $form = $this->createDeleteForm($contact);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($contact);
            $em->flush();
            $this->addFlash('success', 'Contact client supprimé');
        }
        return $this->redirectToRoute('gestion_alumni', ['_fragment' => 'contact']);
    }

    private function createDeleteForm(AlumnusContact $contact)
    {
        return $this->createFormBuilder(['id' => $contact->getId()])
            ->add('id', HiddenType::class)
            ->getForm();
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="alumnus_ajouter", path="/rh/alumnus/ajouter", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function addAlumnus(Request $request, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        $alumnus = new Alumnus();
        $form = $this->createForm(AlumnusType::class, $alumnus);
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($alumnus);
                $em->flush();
                $this->addFlash('success', 'Alumnus enregistré');

                return $this->redirectToRoute('gestion_alumni', ['_fragment' => 'contact']);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }        

        return $this->render('Hr/Alumni/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="alumnus_modifier", path="/hr/alumnus/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifierAlumnus(Request $request, Alumnus $alumnus, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AlumnusType::class, $alumnus);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Alumnus modifié');

                return $this->redirectToRoute('gestion_alumni', ['_fragment' => 'contact']);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }
        $deleteForm = $this->createDeleteFormAlumnus($alumnus);

        return $this->render('Hr/Alumni/modifier.html.twig', [
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'alumnus' => $alumnus,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="alumnus_delete", path="/hr/alumnus/supprimer/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function deleteAlumnus(Alumnus $alumnus, Request $request, EtudePermissionChecker $permChecker)
    {
        $form = $this->createDeleteFormAlumnus($alumnus);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($alumnus);
            $em->flush();
            $this->addFlash('success', 'Alumnus supprimé');
        }
        return $this->redirectToRoute('gestion_alumni', ['_fragment' => 'contact']);
    }

    private function createDeleteFormAlumnus(Alumnus $alumnus)
    {
        return $this->createFormBuilder(['id' => $alumnus->getId()])
            ->add('id', HiddenType::class)
            ->getForm();
    }
}
