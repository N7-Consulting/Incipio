<?php

namespace App\Controller\Archivage;

use App\Controller\Publish\TraitementController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Publish\Document;
use App\Entity\Project\Etude;

class ActicoController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $t) {
        $this->translator = $t;
    }
    // Path pour la génération du document via le Publipostage : DocTypeName/RootName/
    const publipostage = [
        'ce' => TraitementController::DOCTYPE_CONVENTION_ETUDE . '/' .TraitementController::ROOTNAME_ETUDE,
        'cca' => TraitementController::DOCTYPE_CONVENTION_CADRE_AGILE . '/' . TraitementController::ROOTNAME_ETUDE,
        'bdc' => TraitementController::DOCTYPE_BON_COMMANDE . '/' . TraitementController::ROOTNAME_ETUDE,
        'avs' => TraitementController::DOCTYPE_AVENANT . '/' . TraitementController::ROOTNAME_ETUDE,
        'pvis' => TraitementController::DOCTYPE_PROCES_VERBAL_INTERMEDIAIRE . '/' . TraitementController::ROOTNAME_ETUDE,
        'pvr' => TraitementController::DOCTYPE_PROCES_VERBAL_FINAL . '/' . TraitementController::ROOTNAME_ETUDE,
        'missions' => TraitementController::DOCTYPE_RECAPITULATIF_MISSION . '/' . TraitementController::ROOTNAME_MISSION,
    ];

    /**
     * @Route("archivage/actico", name="archivage_actico_index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $etudes = $em->getRepository(Etude::class)->findAll();
        $docs = $em->getRepository(Document::class)->findAll();

        // Correspondance nom dans l'entity Etude <=> colonnes du tableau (affichage)
        $docEtude = $this->getTrans();
        $publipostage = self::publipostage;

        return $this->render('Archivage/Actico/index.html.twig', [
            'controller_name' => 'ActicoController',
            'docs' => $docs,
            'docEtude' => $docEtude,
            'etudes' => $etudes,
            'publipostage' => $publipostage,
        ]);
    }

    private function getTrans() {
        return [
            'ce' => $this->translator->trans('suivi.ce', [], 'project'),
            'cca' => $this->translator->trans('suivi.cca', [], 'project'),
            'bdc' => $this->translator->trans('suivi.bdc', [], 'project'),
            'avs' => $this->translator->trans('suivi.av', [], 'project'),
            'pvis' =>$this->translator->trans('suivi.pvi', [], 'project'),
            'pvr' => $this->translator->trans('suivi.pvr', [], 'project'),
            'missions' => $this->translator->trans('suivi.rm', [], 'project'),
        ];
    }
}
