<?php

namespace App\Twig;

use App\Entity\Personne\Membre;
use App\Entity\Project\DocType;
use App\Entity\Project\Etude;
use App\Entity\Project\Mission;
use App\Entity\Project\ProcesVerbal;
use App\Entity\Treso\Facture;
use App\Entity\User\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EtudeExtension extends AbstractExtension
{
    // Pour utiliser les fonctions depuis twig
    public function getName()
    {
        return 'Twig_EtudeExtension';
    }

    // Pour utiliser les fonctions depuis twig
    public function getFunctions()
    {
        return [
            'getErrors' => new TwigFunction('getErrors', [$this, 'getErrors']),
            'getWarnings' => new TwigFunction('getWarnings', [$this, 'getWarnings']),
            'getEtatDoc' => new TwigFunction('getEtatDoc', [$this, 'getEtatDoc']),
            'getEtatFacture' => new TwigFunction('getEtatFacture', [$this, 'getEtatFacture']),
            'getColor' => new TwigFunction('getColor', [$this, 'getColor']),
            'getColorDoc' => new TwigFunction('getColorDoc', [$this, 'getColorDoc']),
            'confidentielRefus' => new TwigFunction('confidentielRefus', [$this, 'confidentielRefusTwig']),
        ];
    }

    public function toString($int)
    {
        return (string) $int;
    }

    public function nonBreakingSpace($string)
    {
        return preg_replace('#\s#', '&nbsp;', $string);
    }

    public function confidentielRefusTwig(Etude $etude, User $user, $isGranted)
    {
        try {
            if ($etude->getConfidentiel() && !$isGranted) {
                if ($etude->getSuiveur() && $user->getPersonne()->getId() != $etude->getSuiveur()->getId()) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            return true;
        }

        return false;
    }

    public function getErrors(Etude $etude)
    {
        $errors = [];

        /**************************************************
         * Vérification de la cohérence des dateSignature *
         **************************************************/

        // CCA > BDC
        if ($etude->getCca() && $etude->getBdc()) {
            if (
                null !== $etude->getBdc()->getDateSignature() && $etude->getCca()->getDateSignature() > $etude->getBdc()
                ->getDateSignature()
            ) {
                $error = [
                    'titre' => 'CCA, BDC - Date de signature : ',
                    'message' => 'La date de signature de la Convention Cadre Agile doit être antérieure ou égale à la date de signature du Bon de Commande.',
                ];
                array_push($errors, $error);
            }
        }

        // BDC > RM
        if ($etude->getBdc()) {
            foreach ($etude->getMissions() as $mission) {
                if (
                    null !== $mission->getDateSignature() && $etude->getBdc()
                    ->getDateSignature() > $mission->getDateSignature()
                ) {
                    $error = [
                        'titre' => 'RM, BDC  - Date de signature : ',
                        'message' => 'La date de signature du Bon de Commande doit être antérieure ou égale à la date de signature des récapitulatifs de mission.',
                    ];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // BDC > PVRI
        if ($etude->getBdc()) {
            /** @var ProcesVerbal $pvi */
            foreach ($etude->getPvis() as $pvi) {
                if (
                    null !== $pvi->getDateSignature() && $etude->getBdc()
                    ->getDateSignature() >= $pvi->getDateSignature()
                ) {
                    $error = [
                        'titre' => 'PVRIS, BDC  - Date de signature : ',
                        'message' => 'La date de signature du Bon de Commande doit être antérieure à la date de signature des PVRIS.',
                    ];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // BDC > FI
        if ($etude->getBdc()) {
            foreach ($etude->getFactures() as $FactureVente) {
                if (
                    null !== $FactureVente->getDateEmission() && $etude->getBdc()
                    ->getDateSignature() > $FactureVente->getDateEmission()
                ) {
                    $error = [
                        'titre' => 'Factures, BDC  - Date de signature : ',
                        'message' => 'La date de signature du Bon de Commande doit être antérieure à la date de signature des Factures.',
                    ];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // AP > CC
        if ($etude->getAp() && $etude->getCc()) {
            if (
                null !== $etude->getCc()->getDateSignature() && $etude->getAp()->getDateSignature() > $etude->getCc()
                ->getDateSignature()
            ) {
                $error = [
                    'titre' => 'AP, CC - Date de signature : ',
                    'message' => 'La date de signature de l\'Avant Projet doit être antérieure ou égale à la date de signature de la Convention Client.',
                ];
                array_push($errors, $error);
            }
        }

        // CC > RM
        if ($etude->getCc()) {
            foreach ($etude->getMissions() as $mission) {
                if (
                    null !== $mission->getDateSignature() && $etude->getCc()
                    ->getDateSignature() > $mission->getDateSignature()
                ) {
                    $error = [
                        'titre' => 'RM, CC  - Date de signature : ',
                        'message' => 'La date de signature de la Convention Client doit être antérieure ou égale à la date de signature des récapitulatifs de mission.',
                    ];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // CC > PVRI
        if ($etude->getCc()) {
            /** @var ProcesVerbal $pvi */
            foreach ($etude->getPvis() as $pvi) {
                if (
                    null !== $pvi->getDateSignature() && $etude->getCc()
                    ->getDateSignature() >= $pvi->getDateSignature()
                ) {
                    $error = [
                        'titre' => 'PVRIS, CC  - Date de signature : ',
                        'message' => 'La date de signature de la Convention Client doit être antérieure à la date de signature des PVRIS.',
                    ];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // CC > FI
        if ($etude->getCc()) {
            foreach ($etude->getFactures() as $FactureVente) {
                if (
                    null !== $FactureVente->getDateEmission() && $etude->getCc()
                    ->getDateSignature() > $FactureVente->getDateEmission()
                ) {
                    $error = [
                        'titre' => 'Factures, CC  - Date de signature : ',
                        'message' => 'La date de signature de la Convention Client doit être antérieure à la date de signature des Factures.',
                    ];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        //ordre PVRI
        /**
         * @var ProcesVerbal
         * @var ProcesVerbal $pviAnterieur
         */
        foreach ($etude->getPvis() as $pvi) {
            if (isset($pviAnterieur)) {
                if (null !== $pvi->getDateSignature() && $pvi->getDateSignature() < $pviAnterieur->getDateSignature()) {
                    $error = [
                        'titre' => 'PVRIS - Date de signature : ', 'message' => 'La date de signature du PVRI1 doit être antérieure à celle du PVRI2 et ainsi de suite.
           ',
                    ];
                    array_push($errors, $error);
                    break;
                }
            }
            $pviAnterieur = $pvi;
        }

        // PVRF < fin d'étude
        if ($etude->getPvr()) {
            if (null !== $etude->getDateFin(true) && $etude->getPvr()->getDateSignature() > $etude->getDateFin(true)) {
                $error = [
                    'titre' => 'PVRF  - Date de signature : ',
                    'message' => 'La date de signature du PVRF doit être antérieure à la date de fin de l\'étude. Consulter la Convention Client ou l\'Avenant à la Convention Client pour la fin l\'étude.',
                ];
                array_push($errors, $error);
            }
        }

        // CE <= RM
        foreach ($etude->getMissions() as $mission) {
            /** @var Mission $mission */
            if ($intervenant = $mission->getIntervenant()) {
                /** @var Membre $intervenant */
                $dateSignature = $dateDebutOm = null;
                if (null !== $mission->getDateSignature()) {
                    $dateSignature = clone $mission->getDateSignature();
                }
                if (null !== $mission->getDebutOm()) {
                    $dateDebutOm = clone $mission->getDebutOm();
                }
                if (null === $dateSignature || null === $dateDebutOm) {
                    continue;
                }

                $error = [
                    'titre' => 'CE - RM : ' . $intervenant->getPersonne()->getPrenomNom(),
                    'message' => 'La date de signature de la Convention Eleve de ' . $intervenant->getPersonne()
                        ->getPrenomNom() . ' doit être antérieure à la date de signature du récapitulatifs de mission.',
                ];
                $errorAbs = [
                    'titre' => 'CE - RM : ' . $intervenant->getPersonne()->getPrenomNom(),
                    'message' => 'La Convention Eleve de ' . $intervenant->getPersonne()
                        ->getPrenomNom() . ' n\'est pas signée.',
                ];

                if (null === $intervenant->getDateConventionEleve()) {
                    array_push($errors, $errorAbs);
                } elseif (
                    $intervenant->getDateConventionEleve() >= $dateSignature ||
                    $intervenant->getDateConventionEleve() >= $dateDebutOm
                ) {
                    array_push($errors, $error);
                }
            }
        }

        // Date de fin d'étude approche alors que le PVRF n'est pas signé
        $now = new \DateTime('now');
        $DateAvert0 = new \DateInterval('P10D');
        if ($etude->getDateFin()) {
            if (!$etude->getPvr()) {
                if ($now < $etude->getDateFin(true) && $etude->getDateFin(true)->sub($DateAvert0) < $now) {
                    $error = [
                        'titre' => 'Fin de l\'étude :',
                        'message' => 'L\'étude se termine dans moins de dix jours, pensez à faire signer le PVRF ou à faire signer des avenants de délais si vous pensez que l\'étude ne se terminera pas à temps.',
                    ];
                    array_push($errors, $error);
                } elseif ($etude->getDateFin(true) < $now) {
                    $error = [
                        'titre' => 'Fin de l\'étude :',
                        'message' => 'La fin de l\'étude est passée. Pensez à faire un PVRF ou des avenants à la CC et au(x) RM.',
                    ];
                    array_push($errors, $error);
                }
            } else {
                if ($etude->getPvr()->getDateSignature() > $etude->getDateFin(true)) {
                    $error = [
                        'titre' => 'Fin de l\'étude :',
                        'message' => 'La date du PVRF est située après la fin de l\'étude.',
                    ];
                    array_push($errors, $error);
                }
            }
        }

        /*************************
         * Contenu des documents *
         *************************/

        // Description de l'étude suffisante
        if (strlen($etude->getDescriptionPrestation()) < 300) {
            $error = [
                'titre' => 'Description de l\'étude:',
                'message' => 'Attention la description de l\'étude fait moins de 300 caractères',
            ];
            array_push($errors, $error);
        }

        /**************************************************
         * Vérification de la cohérence des JEH reversés  *
         **************************************************/

        // JEH présent dans RM > JEH facturé (ne prend pas en compte les avenants)
        $jehReverses = 0;
        $jehFactures = $etude->getNbrJEH();
        foreach ($etude->getMissions() as $mission) {
            $jehReverses += $mission->getNbrJEH();
        }
        if ($jehReverses > $jehFactures) {
            $error = [
                'titre' => 'Incohérence dans les JEH reversé',
                'message' => "Vous reversez plus de JEH ($jehReverses) que vous n'en n'avez facturé ($jehFactures)",
            ];
            array_push($errors, $error);
        }

        /*****************************************************
         * Vérification de la nationnalité des intervenants  *
         *****************************************************/
        foreach ($etude->getMissions() as $mission) {
            // Vérification de la présence d'intervenant algériens
            $intervenant = $mission->getIntervenant();
            if ($intervenant && 'DZ' == $intervenant->getNationalite()) {
                $error = [
                    'titre' => 'Nationalité des Intervenants',
                    'message' => "L'intervenant " . $intervenant->getPersonne()
                        ->getPrenomNom() . " est de nationnalité algériennne. Il ne peut intervenir sur l'étude.",
                ];
                array_push($errors, $error);
            }
        }

        /**********************************************
         * Verification des dates de debut des phases *
         **********************************************/
        $phasesErreurDate = 0; //compteur des phases avec date incorrectes
        $doc = NULL;

        // Sélection du document d'étude ─ On s'en fiche du message personnalisé
        if ($etude->getCc())
            $doc = $etude->getCc();
        elseif ($etude->getCe())
            $doc = $etude->getCe();
        elseif ($etude->getBdc())
            $doc = $etude->getBdc();

        if ($doc) {
            foreach ($etude->getPhases() as $phase) {
                if ($phase->getDateDebut() < $doc->getDateSignature()) {
                    ++$phasesErreurDate;
                }
            }
        }

        if ($phasesErreurDate > 0) {
            $error = [
                'titre' => 'Date des phases',
                'message' => 'Il y a ' . $phasesErreurDate . ' erreur(s) dans les dates de début de phases.',
            ];
            array_push($errors, $error);
        }

        return $errors;
    }

    public function getWarnings(Etude $etude)
    {
        $warnings = [];

        // Description de l'étude insuffisante
        $length = strlen($etude->getDescriptionPrestation());
        if ($length > 300 && $length < 500) {
            $error = [
                'titre' => 'Description de l\'étude:',
                'message' => 'Attention la description de l\'étude fait moins de 500 caractères',
            ];
            array_push($warnings, $error);
        }

        // Entité sociale absente
        if (null === $etude->getProspect()->getEntite()) {
            $warning = [
                'titre' => 'Entité sociale : ',
                'message' => 'L\'entité sociale est absente. Vérifiez bien que la société est bien enregistrée et toujours en activité.',
            ];
            array_push($warnings, $warning);
        }

        // Etude se termine dans 20 jours
        $now = new \DateTime('now');
        $DateAvert0 = new \DateInterval('P20D');
        $DateAvert1 = new \DateInterval('P10D');
        if ($etude->getDateFin()) {
            if ($etude->getDateFin()->sub($DateAvert1) > $now && $etude->getDateFin()->sub($DateAvert0) < $now) {
                $warning = [
                    'titre' => 'Fin de l\'étude :',
                    'message' => 'l\'étude se termine dans moins de vingt jours, pensez à faire signer le PVRF ou à faire signer des avenants de délais si vous pensez que l\'étude ne se terminera pas à temps.',
                ];
                array_push($warnings, $warning);
            }
        }

        // Date RM Mal renseignée
        // CE + 1w < RM
        /** @var Mission $mission */
        foreach ($etude->getMissions() as $mission) {
            if ($intervenant = $mission->getIntervenant()) {
                $dateSignature = $dateDebutOm = null;
                if (null !== $mission->getDateSignature()) {
                    $dateSignature = clone $mission->getDateSignature();
                }
                if (null !== $mission->getDebutOm()) {
                    $dateDebutOm = clone $mission->getDebutOm();
                }
                if (null === $dateSignature || null === $dateDebutOm) {
                    $warning = [
                        'titre' => 'Dates sur le RM de ' . $intervenant->getPersonne()->getPrenomNom(),
                        'message' => 'Le RM de ' . $intervenant->getPersonne()
                            ->getPrenomNom() . ' est mal rédigé. Vérifiez les dates de signature et de début de mission.',
                    ];
                    array_push($warnings, $warning);
                }
            }
        }

        /*****************************************************
         * Vérification de la nationnalité des intervenants  *
         *****************************************************/
        /** @var Mission $mission */
        foreach ($etude->getMissions() as $mission) {
            // Vérification de la présence d'intervenant étranger non algérien (relevé dans error)
            $intervenant = $mission->getIntervenant();
            if ($intervenant && 'FR' != $intervenant->getNationalite() && 'DZ' != $intervenant->getNationalite()) {
                $warning = [
                    'titre' => 'Nationalité des Intervenants',
                    'message' => "L'intervenant " . $intervenant->getPersonne()
                        ->getPrenomNom() . " n'est pas de nationalité Française. Pensez à faire une Déclaration d'Emploi pour un étudiant Etranger auprès de la préfecture.",
                ];
                array_push($warnings, $warning);
            }
        }

        return $warnings;
    }

    // Never used and deprecated (CC and AP docs) but interesting... 2021
    public function getInfos(Etude $etude)
    {
        $infos = [];
        // Recontacter client
        $DateAvertContactClient = new \DateInterval('P15D');
        $now = new \DateTime('now');
        if (null !== $this->getDernierContact($etude) && $now->sub($DateAvertContactClient) > $this->getDernierContact($etude)) {
            $warning = ['titre' => 'Contact client :', 'message' => 'Recontacter le client'];
            array_push($warnings, $warning);
        }

        if (null !== $etude->getAp()) {
            if ($etude->getAp()->getRedige()) {
                if (!$etude->getAp()->getRelu()) {
                    $info = ['titre' => 'Avant-Projet : ', 'message' => 'à faire relire par le Responsable Qualité'];
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getSpt1()) {
                    $info = ['titre' => 'Avant-Projet : ', 'message' => 'à faire signer par le président'];
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getEnvoye()) {
                    $info = ['titre' => 'Avant-Projet : ', 'message' => 'à envoyer au client'];
                    array_push($infos, $info);
                }
            }
        }

        //CC

        if (null !== $etude->getCc()) {
            if ($etude->getCc()->getRedige()) {
                if (!$etude->getCc()->getRelu()) {
                    $info = [
                        'titre' => 'Convention Client : ',
                        'message' => 'à faire relire par le Responsable Qualité',
                    ];
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getSpt1()) {
                    $info = [
                        'titre' => 'Convention Client : ',
                        'message' => 'à faire signer par le signer par le président',
                    ];
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getEnvoye()) {
                    $info = ['titre' => 'Convention Client : ', 'message' => 'à envoyer au client'];
                    array_push($infos, $info);
                }
            }
        }

        //Recrutement et RM
        if (null !== $etude->getCc() & null !== $etude->getAp()) {
            if ($etude->getCc()->getSpt2() & $etude->getAp()->getSpt2() & !$etude->getMailEntretienEnvoye()) {
                $info = ['titre' => 'Recrutement : ', 'message' => 'lancez le recrutement des intervenants'];
                array_push($infos, $info);
            }
        }

        foreach ($etude->getMissions() as $mission) {
            if (!$mission->getRedige()) {
                $info = ['titre' => 'Récapitulatif de mission : ', 'message' => 'à rédiger'];
                array_push($infos, $info);
                break;
            } else {
                if (!$mission->getRelu()) {
                    $info = [
                        'titre' => 'Récapitulatif de mission : ',
                        'message' => 'à faire relire par le responsable qualité',
                    ];
                    array_push($infos, $info);
                    break;
                } elseif (!$mission->getSpt1() || !$mission->getSpt2()) {
                    if (!$mission->getSpt1()) {
                        $info = [
                            'titre' => 'Récapitulatif de mission : ',
                            'message' => 'à faire signer, parapher et tamponner par le président',
                        ];
                        array_push($infos, $info);
                    }

                    if (!$mission->getSpt2()) {
                        $info = [
                            'titre' => 'Récapitulatif de mission : ',
                            'message' => 'à faire signer par l\'intervenant',
                        ];
                        array_push($infos, $info);
                    }
                    break;
                }
            }
        }

        return $infos;
    }

    public function getEtatDoc($doc)
    {
        /** @var DocType $doc */
        if (null !== $doc) {
            $ok = $doc->getRedige()
                && $doc->getRelu()
                && $doc->getEnvoye()
                && $doc->getReceptionne();

            $ok = ($ok ? 2 : ($doc->getRedige() ? 1 : 0));
        } else {
            $ok = 0;
        }

        return $ok;
    }

    //Copie de getEtatDoc pour les factures. Les factures n'étendant pas Doctype, le relu, rédigé ...
    // n'est pas pertinent. On ne teste donc que l'existence et le versement.

    /**
     * @param $doc
     *
     * @return $ok : 0=> null, 1 => emis, 2=>recu
     */
    public function getEtatFacture($doc)
    {
        /** @var Facture $doc */
        if (null !== $doc) {
            $now = new \DateTime('now');
            $dateDebutEtude = $doc->getEtude()->getDateLancement();
            $ok = ($doc->getDateVersement() < $now && $doc->getDateVersement() > $dateDebutEtude ? 2 : 1);
        } else {
            $ok = 0;
        }

        return $ok;
    }

    private function getDernierContact(Etude $etude)
    {
        $dernierContact = [];
        if (null !== $etude->getClientContacts()) {
            foreach ($etude->getClientContacts() as $contact) {
                if (null !== $contact->getDate()) {
                    array_push($dernierContact, $contact->getDate());
                }
            }
        }
        if (count($dernierContact) > 0) {
            return max($dernierContact);
        }

        return null;
    }

    /**
     * Avoir une couleur affichée cohérente sur l'ensemble du site
     */
    public function getColor(Etude $etude) {
        switch ($etude->getStateID()) {
            case Etude::ETUDE_STATE_ACCEPTEE:
            case Etude::ETUDE_STATE_COURS:
            case Etude::ETUDE_STATE_CLOTUREE:
                $color = 'success';
                break;
            case Etude::ETUDE_STATE_AVORTEE:
                $color = 'danger';
                break;
            case Etude::ETUDE_STATE_PAUSE:
                $color = 'warning';
                break;
            case Etude::ETUDE_STATE_NEGOCIATION:
            case Etude::ETUDE_STATE_FINIE:
                $color = 'info';
                break;
            default:
                $color = '';
                break;
        }

        return 'alert-' . $color;
    }

    /**
     * Avoir une couleur affichée cohérente sur l'ensemble du site
     * Utilisé principalement dans Vu(e)CA
     */
    public function getColorDoc($doc) {

        if ($doc instanceof Facture) {
            $etat = $this->getEtatFacture($doc);
        } else {
            $etat = $this->getEtatDoc($doc);
        }

        switch ($etat) {
            case 0:
                $color = 'danger';
                break;
            case 1:
                $color = 'warning';
                break;
            case 2:
                $color = 'success';
                break;
            default:
                $color = '';
                break;
        }

        return $color;
    }
}
