<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use Mgate\SuiviBundle\Entity\Etude as Etude;
use Mgate\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class EtudeManager extends \Twig_Extension
{
    protected $em;
    protected $tva;
    protected $namingConvention;

    public function __construct(EntityManager $em, KeyValueStore $keyValueStore)
    {
        $this->em = $em;
        if ($keyValueStore->exists('tva')) {
            $this->tva = $keyValueStore->get('tva');
        } else {
            throw new \LogicException('Parameter TVA is undefined.');
        }

        if ($keyValueStore->exists('namingConvention')) {
            $namingConvention = $keyValueStore->get('namingConvention');
            $this->namingConvention = ($namingConvention === 'nom' || $namingConvention === 'numero' ?
                $namingConvention : 'id');
        } else {
            $this->namingConvention = 'id';
        }

    }

    // Pour utiliser les fonctions depuis twig
    public function getName()
    {
        return 'Mgate_EtudeManager';
    }

    // Pour utiliser les fonctions depuis twig
    public function getFunctions()
    {
        return array(
            'getErrors' => new \Twig_Function_Method($this, 'getErrors'),
            'getWarnings' => new \Twig_Function_Method($this, 'getWarnings'),
            'getInfos' => new \Twig_Function_Method($this, 'getInfos'),
            'getEtatDoc' => new \Twig_Function_Method($this, 'getEtatDoc'),
            'getEtatFacture' => new \Twig_Function_Method($this, 'getEtatFacture'),
            'confidentielRefus' => new \Twig_Function_Method($this, 'confidentielRefusTwig'),
        );
    }

    /***
     *
     * Juste un test
     */
    public function getFilters()
    {
        return array(
            'nbsp' => new \Twig_Filter_Method($this, 'nonBreakingSpace'),
            'string' => new \Twig_Filter_Method($this, 'toString'),
        );
    }

    public function toString($int)
    {
        return (string)$int;
    }

    public function nonBreakingSpace($string)
    {
        return preg_replace('#\s#', '&nbsp;', $string);
    }

    /**
     * @param Etude $etude
     * @param User $user
     * @param AuthorizationChecker $userToken
     *
     * @return bool
     *              Comme l'authorizationChecker n'est pas dispo coté twig, on utilisera cette méthode uniquement dans les controllers.
     *              Pour twig, utiliser confidentielRefusTwig(Etude, User, is_granted('ROLE_SOUHAITE'))
     */
    public function confidentielRefus(Etude $etude, User $user, AuthorizationChecker $userToken)
    {
        try {
            if ($etude->getConfidentiel() && !$userToken->isGranted('ROLE_CA')) {
                if ($etude->getSuiveur() && $user->getPersonne()->getId() != $etude->getSuiveur()->getId()) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            return true;
        }

        return false;
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

    /**
     * Get montant total TTC.
     *
     * @param Etude $etude
     *
     * @return float
     */
    public function getTotalTTC(Etude $etude)
    {
        return round($etude->getMontantHT() * (1 + $this->tva), 2);
    }

    /**
     * Get nombre de JEH.
     *
     * @param Etude $etude
     *
     * @return float
     */
    public function getMontantVerse(Etude $etude)
    {
        $total = 0;

        foreach ($etude->getMissions() as $mission) {
            foreach ($etude->getPhases() as $phase) {
                $prix = $phase->getPrixJEH();
                //TO DO faire le cas des prix de jeh différent
            }
            $total = 0.6 * $mission->getNbjeh() * $prix;
        }

        return round($total);
    }

    /*
     * Get référence du document
     * Params : Etude $etude, mixed $doc, string $type (the type of doc)
     */
    public function getRefDoc(Etude $etude, $type, $key = -1)
    {
        $type = strtoupper($type);
        $name = ($this->namingConvention === 'nom' ? $etude->getNom() :
            $this->namingConvention === 'numero' ? $etude->getNumero() : $etude->getId());
        if ($type == 'AP') {
            if ($etude->getAp()) {
                return $name . '-' . $type . '-' . $etude->getAp()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ($type == 'CC') {
            if ($etude->getCc()) {
                return $name . '-' . $type . '-' . $etude->getCc()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ($type == 'RM' || $type == 'DM') {
            if ($key < 0) {
                return $name . '-' . $type;
            }
            if (!$etude->getMissions()->get($key)
                || !$etude->getMissions()->get($key)->getIntervenant()
            ) {
                return $name . '-' . $type . '- ERROR GETTING DEV ID - ERROR GETTING VERSION';
            } else {
                return $name . '-' . $type . '-' . $etude->getMissions()->get($key)->getIntervenant()->getIdentifiant() . '-' . $etude->getMissions()->get($key)->getVersion();
            }
        } elseif ($type == 'FA') {
            return $name . '-' . $type;
        } elseif ($type == 'FI') {
            return $name . '-' . $type . ($key + 1);
        } elseif ($type == 'FS') {
            return $name . '-' . $type;
        } elseif ($type == 'PVI') {
            if ($key >= 0 && $etude->getPvis($key)) {
                return $name . '-' . $type . ($key + 1) . '-' . $etude->getPvis($key)->getVersion();
            } else {
                return $name . '-' . $type . ($key + 1) . '- ERROR GETTING PVI';
            }
        } elseif ($type == 'PVR') {
            if ($etude->getPvr()) {
                return $name . '-' . $type . '-' . $etude->getPvr()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ($type == 'CE') {
            if (!$etude->getMissions()->get($key)
                || !$etude->getMissions()->get($key)->getIntervenant()
            ) {
                return $etude->getMandat() . '-CE- ERROR GETTING DEV ID';
            } else {
                $identifiant = $etude->getMissions()->get($key)->getIntervenant()->getIdentifiant();
            }

            return $etude->getMandat() . '-CE-' . $identifiant;
        } elseif ($type == 'AVCC') {
            if ($etude->getCc() && $etude->getAvs()->get($key)) {
                return $name . '-CC-' . $etude->getCc()->getVersion() . '-AV' . ($key + 1) . '-' . $etude->getAvs()->get($key)->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } else {
            return 'ERROR';
        }
    }

    /**
     * Get nouveau numéro d'etude, pour valeur par defaut dans formulaire.
     */
    public function getNouveauNumero()
    {
        $mandat = $this->getMaxMandat();
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.num')
            ->from('MgateSuiviBundle:Etude', 'e')
            ->andWhere('e.mandat = :mandat')
            ->setParameter('mandat', $mandat)
            ->orderBy('e.num', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value) {
            return $value['num'] + 1;
        } else {
            return 1;
        }
    }

    /**
     * Get nouveau numéro pour FactureVente (auto incrémentation).
     */
    public function getNouveauNumeroFactureVente()
    {
        $qb = $this->em->createQueryBuilder();

        $mandat = 2007 + $this->getMaxMandat();

        $mandatComptable = \DateTime::createFromFormat('d/m/Y', '31/03/' . $mandat);

        $query = $qb->select('e.num')
            ->from('MgateSuiviBundle:FactureVente', 'e')
            ->andWhere('e.dateSignature > :mandatComptable')
            ->setParameter('mandatComptable', $mandatComptable)
            ->orderBy('e.num', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value) {
            return $value['num'] + 1;
        } else {
            return 1;
        }
    }

    public function getExerciceComptable($FactureVente)
    {
        if ($FactureVente) {
            $dateAn = (int)$FactureVente->getDateSignature()->format('y');
            $exercice = ((int)$FactureVente->getDateSignature()->format('m') < 4 ? $dateAn - 8 : $dateAn - 7);

            return $exercice;
        } else {
            return 0;
        }
    }

    public function getDernierContact(Etude $etude)
    {
        $dernierContact = array();
        if ($etude->getClientContacts() !== null) {
            foreach ($etude->getClientContacts() as $contact) {
                if ($contact->getDate() !== null) {
                    array_push($dernierContact, $contact->getDate());
                }
            }
        }
        if (count($dernierContact) > 0) {
            return max($dernierContact);
        } else {
            return;
        }
    }

    public function getRepository()
    {
        return $this->em->getRepository('MgateSuiviBundle:Etude');
    }

    public function getErrors(Etude $etude)
    {
        $errors = array();

        /**************************************************
         * Vérification de la cohérence des dateSignature *
         **************************************************/

        // AP > CC
        if ($etude->getAp() && $etude->getCc()) {
            if ($etude->getCc()->getDateSignature() !== null && $etude->getAp()->getDateSignature() > $etude->getCc()->getDateSignature()) {
                $error = array('titre' => 'AP, CC - Date de signature : ', 'message' => 'La date de signature de l\'Avant Projet doit être antérieure ou égale à la date de signature de la Convention Client.');
                array_push($errors, $error);
            }
        }

        // CC > RM
        if ($etude->getCc()) {
            foreach ($etude->getMissions() as $mission) {
                if ($mission->getDateSignature() !== null && $etude->getCc()->getDateSignature() > $mission->getDateSignature()) {
                    $error = array('titre' => 'RM, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure ou égale à la date de signature des récapitulatifs de mission.');
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // CC > PVI
        if ($etude->getCc()) {
            foreach ($etude->getPvis() as $pvi) {
                if ($pvi->getDateSignature() !== null && $etude->getCc()->getDateSignature() >= $pvi->getDateSignature()) {
                    $error = array('titre' => 'PVIS, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure à la date de signature des PVIS.');
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // CC > FI
        if ($etude->getCc()) {
            foreach ($etude->getFactures() as $FactureVente) {
                if ($FactureVente->getDateEmission() !== null && $etude->getCc()->getDateSignature() > $FactureVente->getDateEmission()) {
                    $error = array('titre' => 'Factures, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure à la date de signature des Factures.');
                    array_push($errors, $error);
                    break;
                }
            }
        }

        //ordre PVI
        foreach ($etude->getPvis() as $pvi) {
            if (isset($pviAnterieur)) {
                if ($pvi->getDateSignature() !== null && $pvi->getDateSignature() < $pviAnterieur->getDateSignature()) {
                    $error = array('titre' => 'PVIS - Date de signature : ', 'message' => 'La date de signature du PVI1 doit être antérieure à celle du PVI2 et ainsi de suite.
           ');
                    array_push($errors, $error);
                    break;
                }
            }
            $pviAnterieur = $pvi;
        }

        // PVR < fin d'étude
        if ($etude->getPvr()) {
            if ($etude->getDateFin(true) !== null && $etude->getPvr()->getDateSignature() > $etude->getDateFin(true)) {
                $error = array('titre' => 'PVR  - Date de signature : ', 'message' => 'La date de signature du PVR doit être antérieure à la date de fin de l\'étude. Consulter la Convention Client ou l\'Avenant à la Convention Client pour la fin l\'étude.');
                array_push($errors, $error);
            }
        }

        // CE <= RM
        foreach ($etude->getMissions() as $mission) {
            if ($intervenant = $mission->getIntervenant()) {
                $dateSignature = $dateDebutOm = null;
                if ($mission->getDateSignature() !== null) {
                    $dateSignature = clone $mission->getDateSignature();
                }
                if ($mission->getDebutOm() !== null) {
                    $dateDebutOm = clone $mission->getDebutOm();
                }
                if ($dateSignature === null || $dateDebutOm === null) {
                    continue;
                }

                $error = array('titre' => 'CE - RM : ' . $intervenant->getPersonne()->getPrenomNom(), 'message' => 'La date de signature de la Convention Eleve de ' . $intervenant->getPersonne()->getPrenomNom() . ' doit être antérieure à la date de signature du récapitulatifs de mission.');
                $errorAbs = array('titre' => 'CE - RM : ' . $intervenant->getPersonne()->getPrenomNom(), 'message' => 'La Convention Eleve de ' . $intervenant->getPersonne()->getPrenomNom() . ' n\'est pas signée.');

                if ($intervenant->getDateConventionEleve() === null) {
                    array_push($errors, $errorAbs);
                } elseif ($intervenant->getDateConventionEleve() >= $dateSignature ||
                    $intervenant->getDateConventionEleve() >= $dateDebutOm
                ) {
                    array_push($errors, $error);
                }
            }
        }

        // Date de fin d'étude approche alors que le PVR n'est pas signé
        $now = new \DateTime('now');
        $DateAvert0 = new \DateInterval('P10D');
        if ($etude->getDateFin()) {
            if (!$etude->getPvr()) {
                if ($now < $etude->getDateFin(true) && $etude->getDateFin(true)->sub($DateAvert0) < $now) {
                    $error = array('titre' => 'Fin de l\'étude :', 'message' => 'L\'étude se termine dans moins de dix jours, pensez à faire signer le PVR ou à faire signer des avenants de délais si vous pensez que l\'étude ne se terminera pas à temps.');
                    array_push($errors, $error);
                } elseif ($etude->getDateFin(true) < $now) {
                    $error = array('titre' => 'Fin de l\'étude :', 'message' => 'La fin de l\'étude est passée. Pensez à faire un PVR ou des avenants à la CC et au(x) RM.');
                    array_push($errors, $error);
                }
            } else {
                if ($etude->getPvr()->getDateSignature() > $etude->getDateFin(true)) {
                    $error = array('titre' => 'Fin de l\'étude :', 'message' => 'La date du PVR est située après la fin de l\'étude.');
                    array_push($errors, $error);
                }
            }
        }

        /*************************
         * Contenu des documents *
         *************************/

        // Description de l'AP suffisante
        if (strlen($etude->getDescriptionPrestation()) < 300) {
            $error = array('titre' => 'Description de l\'étude:', 'message' => 'Attention la description de l\'étude dans l\'AP fait moins de 300 caractères');
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
            $error = array('titre' => 'Incohérence dans les JEH reversé', 'message' => "Vous reversez plus de JEH ($jehReverses) que vous n'en n'avez facturé ($jehFactures)");
            array_push($errors, $error);
        }

        /*****************************************************
         * Vérification de la nationnalité des intervenants  *
         *****************************************************/
        foreach ($etude->getMissions() as $mission) {
            // Vérification de la présence d'intervenant algériens
            $intervenant = $mission->getIntervenant();
            if ($intervenant && $intervenant->getNationalite() == 'DZ') {
                $error = array('titre' => 'Nationalité des Intervenants', 'message' => "L'intervenant " . $intervenant->getPersonne()->getPrenomNom() . " est de nationnalité algériennne. Il ne peut intervenir sur l'étude.");
                array_push($errors, $error);
            }
        }

        /*
         * Verification que les dates de debut de phases correspondent bien avec la date de signature de la CC
         * On créé juste un compteur d'erreur pour ne pas spammer l'utilisateur sous un grand nombre d'erreurs liées juste aux phases.
         */
        $phasesErreurDate = 0; //compteur des phases avec date incorrectes
        if ($etude->getCc() !== null) {
            foreach ($etude->getPhases() as $phase) {
                if ($phase->getDateDebut() < $etude->getCc()->getDateSignature()) {
                    ++$phasesErreurDate;
                }
            }
            if ($phasesErreurDate > 0) {
                $error = array('titre' => 'Date des phases', 'message' => 'Il y a ' . $phasesErreurDate . ' erreur(s) dans les dates de début de phases.');
                array_push($errors, $error);
            }
        }

        return $errors;
    }

    public function getWarnings(Etude $etude)
    {
        $warnings = array();

        // Description de l'AP insuffisante
        $length = strlen($etude->getDescriptionPrestation());
        if ($length > 300 && $length < 500) {
            $error = array('titre' => 'Description de l\'étude:', 'message' => 'Attention la description de l\'étude dans l\'AP fait moins de 500 caractères');
            array_push($warnings, $error);
        }

        // Entité sociale absente
        if ($etude->getProspect()->getEntite() === null) {
            $warning = array('titre' => 'Entité sociale : ', 'message' => 'L\'entité sociale est absente. Vérifiez bien que la société est bien enregistrée et toujours en activité.');
            array_push($warnings, $warning);
        }

        // Etude se termine dans 20 jours
        $now = new \DateTime('now');
        $DateAvert0 = new \DateInterval('P20D');
        $DateAvert1 = new \DateInterval('P10D');
        if ($etude->getDateFin()) {
            if ($etude->getDateFin()->sub($DateAvert1) > $now && $etude->getDateFin()->sub($DateAvert0) < $now) {
                $warning = array('titre' => 'Fin de l\'étude :', 'message' => 'l\'étude se termine dans moins de vingt jours, pensez à faire signer le PVR ou à faire signer des avenants de délais si vous pensez que l\'étude ne se terminera pas à temps.');
                array_push($warnings, $warning);
            }
        }

        // Date RM Mal renseignée
        // CE + 1w < RM
        foreach ($etude->getMissions() as $mission) {
            if ($intervenant = $mission->getIntervenant()) {
                $dateSignature = $dateDebutOm = null;
                if ($mission->getDateSignature() !== null) {
                    $dateSignature = clone $mission->getDateSignature();
                }
                if ($mission->getDebutOm() !== null) {
                    $dateDebutOm = clone $mission->getDebutOm();
                }
                if ($dateSignature === null || $dateDebutOm === null) {
                    $warning = array('titre' => 'Dates sur le RM de ' . $intervenant->getPersonne()->getPrenomNom(), 'message' => 'Le RM de ' . $intervenant->getPersonne()->getPrenomNom() . ' est mal rédigé. Vérifiez les dates de signature et de début de mission.');
                    array_push($warnings, $warning);
                }
            }
        }

        /*****************************************************
         * Vérification de la nationnalité des intervenants  *
         *****************************************************/
        foreach ($etude->getMissions() as $mission) {
            // Vérification de la présence d'intervenant étranger non algérien (relevé dans error)
            $intervenant = $mission->getIntervenant();
            if ($intervenant && $intervenant->getNationalite() != 'FR' && $intervenant->getNationalite() != 'DZ') {
                $warning = array('titre' => 'Nationalité des Intervenants', 'message' => "L'intervenant " . $intervenant->getPersonne()->getPrenomNom() . " n'est pas de nationalité Française. Pensez à faire une Déclaration d'Emploi pour un étudiant Etranger auprès de la préfecture.");
                array_push($warnings, $warning);
            }
        }

        return $warnings;
    }

    public function getInfos(Etude $etude)
    {
        $infos = array();
        // Recontacter client
        $DateAvertContactClient = new \DateInterval('P15D');
        if ($this->getDernierContact($etude) !== null && $now->sub($DateAvertContactClient) > $this->getDernierContact($etude)) {
            $warning = array('titre' => 'Contact client :', 'message' => 'Recontacter le client');
            array_push($warnings, $warning);
        }

        if ($etude->getAp() !== null) {
            if ($etude->getAp()->getRedige()) {
                if (!$etude->getAp()->getRelu()) {
                    $info = array('titre' => 'Avant-Projet : ', 'message' => 'à faire relire par le Responsable Qualité');
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getSpt1()) {
                    $info = array('titre' => 'Avant-Projet : ', 'message' => 'à faire signer par le président');
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getEnvoye()) {
                    $info = array('titre' => 'Avant-Projet : ', 'message' => 'à envoyer au client');
                    array_push($infos, $info);
                }
            }
        }

        //CC

        if ($etude->getCc() !== null) {
            if ($etude->getCc()->getRedige()) {
                if (!$etude->getCc()->getRelu()) {
                    $info = array('titre' => 'Convention Client : ', 'message' => 'à faire relire par le Responsable Qualité');
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getSpt1()) {
                    $info = array('titre' => 'Convention Client : ', 'message' => 'à faire signer par le signer par le président');
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getEnvoye()) {
                    $info = array('titre' => 'Convention Client : ', 'message' => 'à envoyer au client');
                    array_push($infos, $info);
                }
            }
        }

        //Recrutement et RM
        if ($etude->getCc() !== null & $etude->getAp() !== null) {
            if ($etude->getCc()->getSpt2() & $etude->getAp()->getSpt2() & !$etude->getMailEntretienEnvoye()) {
                $info = array('titre' => 'Recrutement : ', 'message' => 'lancez le recrutement des intervenants');
                array_push($infos, $info);
            }
        }

        foreach ($etude->getMissions() as $mission) {
            if (!$mission->getRedige()) {
                $info = array('titre' => 'Récapitulatif de mission : ', 'message' => 'à rédiger');
                array_push($infos, $info);
                break;
            } else {
                if (!$mission->getRelu()) {
                    $info = array('titre' => 'Récapitulatif de mission : ', 'message' => 'à faire relire par le responsable qualité');
                    array_push($infos, $info);
                    break;
                } elseif (!$mission->getSpt1() || !$mission->getSpt2()) {
                    if (!$mission->getSpt1()) {
                        $info = array('titre' => 'Récapitulatif de mission : ', 'message' => 'à faire signer, parapher et tamponner par le président');
                        array_push($infos, $info);
                    }

                    if (!$mission->getSpt2()) {
                        $info = array('titre' => 'Récapitulatif de mission : ', 'message' => 'à faire signer par l\'intervenant');
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
        if ($doc !== null) {
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

    //Copie de getEtatDoc pour les factures. Les factures n'étendant pas Doctype, le relu, rédigé ... n'est pas pertinent. On ne teste donc que l'existence et loe versement.

    /**
     * @param $doc
     *
     * @return $ok : 0=> null, 1 => emis, 2=>recu
     */
    public function getEtatFacture($doc)
    {
        if ($doc !== null) {
            $now = new \DateTime('now');
            $dateDebutEtude = $doc->getEtude()->getCc()->getDateSignature();
            $ok = ($doc->getDateVersement() < $now && $doc->getDateVersement() > $dateDebutEtude ? 2 : 1);
        } else {
            $ok = 0;
        }

        return $ok;
    }

    /**
     * Converti le numero de mandat en année.
     */
    public function mandatToString($idMandat)
    {
        // Mandat 0 => 2007/2008

        return strval(2007 + $idMandat) . '/' . strval(2008 + $idMandat);
    }

    /**
     * Get le maximum des mandats.
     */
    public function getMaxMandat()
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.mandat')
            ->from('MgateSuiviBundle:Etude', 'e')
            ->orderBy('e.mandat', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value) {
            return $value['mandat'];
        } else {
            return 0;
        }
    }

    /**
     * Get le minimum des mandats.
     */
    public function getMinMandat()
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.mandat')
            ->from('MgateSuiviBundle:Etude', 'e')
            ->orderBy('e.mandat', 'ASC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value) {
            return $value['mandat'];
        } else {
            return 0;
        }
    }

    /**
     * Get le maximum des mandats par rapport à la date de Signature de signature des CC.
     */
    public function getMaxMandatCc()
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('c.dateSignature')
            ->from('MgateSuiviBundle:Cc', 'c')
            ->orderBy('c.dateSignature', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();

        if ($value) {
            return $this->dateToMandat($value['dateSignature']);
        } else {
            return 0;
        }
    }

    /**
     * Converti le numero de mandat en année.
     */
    public function dateToMandat(\DateTime $date)
    {
        // Mandat 0 => 2007/2008
        $interval = new \DateInterval('P2M20D');
        $date2 = clone $date;
        $date2->sub($interval);

        return intval($date2->format('Y')) - 2007;
    }

    /**
     * Taux de conversion.
     */
    public function getTauxConversion()
    {
        $tauxConversion = array();
        $tauxConversionCalc = array();

        //recup toute les etudes

        foreach ($this->getRepository()->findAll() as $etude) {
            $mandat = $etude->getMandat();
            if ($etude->getAp() !== null) {
                if ($etude->getAp()->getSpt2()) {
                    if (isset($tauxConversion[$mandat])) {
                        $ApRedige = $tauxConversion[$mandat]['ap_redige'];
                        ++$ApRedige;
                        $ApSigne = $tauxConversion[$mandat]['ap_signe'];
                        ++$ApSigne;
                    } else {
                        $ApRedige = 1;
                        $ApSigne = 1;
                    }
                    $tauxConversionCalc = array('mandat' => $mandat, 'ap_redige' => $ApRedige, 'ap_signe' => $ApSigne);
                    $tauxConversion[$mandat] = $tauxConversionCalc;
                } elseif ($etude->getAp()->getRedige()) {
                    if (isset($tauxConversion[$mandat])) {
                        $ApRedige = $tauxConversion[$mandat]['ap_redige'];
                        ++$ApRedige;
                        $ApSigne = $tauxConversion[$mandat]['ap_signe'];
                    } else {
                        $ApRedige = 1;
                        $ApSigne = 0;
                    }
                    $tauxConversionCalc = array('mandat' => $mandat, 'ap_redige' => $ApRedige, 'ap_signe' => $ApSigne);
                    $tauxConversion[$mandat] = $tauxConversionCalc;
                }
            }
        }

        return $tauxConversion;
    }
}
