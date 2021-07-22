<?php

namespace App\Controller\Archivage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Treso\Facture;
use App\Entity\Publish\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TresoController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_TRESO')")
     * @Route("archivage/treso", name="archivage_treso_index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository(Facture::class)->getFactures();
        $entities = $em->getRepository(Document::class)->findAll();

        return $this->render('Archivage/Treso/index.html.twig', [
            'controller_name' => 'TresoController',
            'factures' => $factures,
            'docs' => $entities,
        ]);
    }
}
