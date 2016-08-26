<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Form\DocumentType;

class DocumentController extends Controller
{
    /**
     * @Security("has_role('ROLE_CA')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePubliBundle:Document')->findAll();

        $totalSize = 0;
        foreach ($entities as $entity) {
            $totalSize += $entity->getSize();
        }

        return $this->render('mgatePubliBundle:Document:index.html.twig', array(
            'docs' => $entities,
            'totalSize' => $totalSize,
        ));
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function uploadEtudeAction($etude_id)
    {
        $em = $this->getDoctrine()->getManager();
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->getByNom($etude_id);

        if (!$etude) {
            throw $this->createNotFoundException('Le document ne peut être lié à une étude qui n\'existe pas!');
        }

        if ($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context'))) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Cette étude est confidentielle !');
        }

        $options['etude'] = $etude;

        if (!$response = $this->upload(false, $options)) {
            // Si tout est ok
            return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('nom' => $etude->getNom())));
        } else {
            return $response;
        }
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function uploadEtudiantAction($membre_id)
    {
        $em = $this->getDoctrine()->getManager();
        $membre = $em->getRepository('mgatePersonneBundle:Membre')->find($membre_id);

        if (!$membre) {
            throw $this->createNotFoundException('Le document ne peut être lié à un membre qui n\'existe pas!');
        }

        $options['etudiant'] = $membre;

        if (!$response = $this->upload(false, $options)) {
            // Si tout est ok
            return $this->redirect($this->generateUrl('mgatePersonne_membre_voir', array('id' => $membre_id)));
        } else {
            return $response;
        }
    }

    /**
     * @Security("has_role('ROLE_CA')")
     */
    public function uploadFormationAction($id)
    {
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function uploadDoctypeAction()
    {
        if (!$response = $this->upload(true)) {
            // Si tout est ok
            return $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));
        } else {
            return $response;
        }
    }

    /**
     * @Security("has_role('ROLE_CA')")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$doc = $em->getRepository('mgatePubliBundle:Document')->find($id)) {
            throw $this->createNotFoundException('Le Document n\'existe pas !');
        }

        if ($doc->getRelation()) { // Cascade sucks
            $relation = $doc->getRelation()->setDocument();
            $doc->setRelation();
            $em->remove($relation);
            $em->flush();
        }

        $em->remove($doc);
        $em->flush();

        return $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));
    }

    private function upload($deleteIfExist = false, $options = array())
    {
        $document = new Document();
        if (count($options)) {
            $relatedDocument = new \mgate\PubliBundle\Entity\RelatedDocument();
            $relatedDocument->setDocument($document);
            $document->setRelation($relatedDocument);
            if (key_exists('etude', $options)) {
                $relatedDocument->setEtude($options['etude']);
            }
            if (key_exists('etudiant', $options)) {
                $relatedDocument->setMembre($options['etudiant']);
            }
        }

        $form = $this->createForm(new DocumentType(), $document, $options);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $documentManager = $this->get('mgate.document_manager');
                $documentManager->uploadDocument($document, null, $deleteIfExist);

                return false;
            }
        }

        return $this->render('mgatePubliBundle:Document:upload.html.twig', array('form' => $form->createView()));
    }
}
