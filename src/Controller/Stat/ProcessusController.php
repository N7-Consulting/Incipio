<?php

namespace App\Controller\Stat;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Processus\Processus;
use App\Form\Processus\ProcessType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\KernelInterface;

use App\Entity\Publish\Document;
use App\Entity\Publish\RelatedDocument;
use App\Form\Publish\DocumentType;
use App\Service\Publish\DocumentManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Controller\Stat\UploadedFile;
use App\Controller\Publish\DocumentController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class ProcessusController extends AbstractController
{
    /**
     * @Route("/Processus", name="tab_process")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $processus = $em->getRepository(Processus::class)->findAll();
        
        return $this->render('Stat/Processus/index.html.twig', [
            'controller_name' => 'ProcessusController',
            'processus' => $processus,
        ]);
    }

/**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="ajouter_processus", path="/Processus/Ajouter", methods={"GET","HEAD","POST"})
     *
     * @return Response
     */
    public function ajouter(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $processus = new Processus();
        $form = $this->createForm(ProcessType::class, $processus);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($processus);
                $em->flush();
                $this->addFlash('success', 'Processus enregistré');

                return $this->redirectToRoute('tab_process');
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Stat/Processus/addProcessus.html.twig',  ['form' => $form->createView()]
                                                                                
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(name="processus_supprimer", path="/Processus/Supprimer/{nom}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function deleteProcessus(Processus $processus, Request $request, KernelInterface $kernel)
    {   $em = $this->getDoctrine()->getManager();
        $docs = $processus->getRelatedDocuments();       
        foreach ($docs as $document) {
            $doc = $document->getDocument();
            DocumentController::deleteRelated($em,$doc,$kernel);
        }
        $em->remove($processus);
        $em->flush();
        return $this->redirectToRoute('tab_process');
    }

    /**
     * @Route(name="voir_doc_processus", path="/Processus/Document/Associes/{id}", methods={"GET","HEAD","POST"})
     *
     * @return Response
     */
    public function voirDoc(Processus $processus, Request $request)
    {
        return $this->render('Stat/Processus/processDocuments.html.twig', [
            'processus' => $processus,
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     * @Route(name="modifier_processus", path="/Stat/Processus/Modifier/{id}", methods={"GET","HEAD","POST"}, requirements={"id": "\d+"})
     *
     * @param Processus $processus The training to modify
     *
     * @return Response
     */
    public function modifier(Request $request, Processus $processus)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProcessType::class, $processus);
        $deleteForm = $this->createDeleteForm($processus->getId());

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($processus);
                $em->flush();
                $this->addFlash('success', 'Processus enregistré');

                return $this->redirectToRoute('tab_process', ['id' => $processus->getId()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render('Stat/Processus/modifier.html.twig', [
            'delete_form' => $deleteForm->createView(),
            'form' => $form->createView(),
            'processus' => $processus,
        ]);
    }

        /**
     * Function to create a form to remove a Processus.
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