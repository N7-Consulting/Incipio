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
use Mgate\PersonneBundle\Entity\Membre;
use Mgate\SuiviBundle\Entity\Etude as Etude;
use Mgate\SuiviBundle\Entity\Mission;
use Mgate\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class EtudeManager extends \Twig_Extension
{
    protected $em;

    protected $authorizationChecker;

    protected $tva;

    protected $namingConvention;

    protected $anneeCreation;

    protected $defaultFraisDossier;

    protected $defaultPourcentageAcompte;

    public function __construct(EntityManager $em, KeyValueStore $keyValueStore, AuthorizationChecker $authorizationChecker)
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        if ($keyValueStore->exists('tva')) {
            $this->tva = $keyValueStore->get('tva');
        } else {
            throw new \LogicException('Parameter TVA is undefined.');
        }

        if ($keyValueStore->exists('namingConvention')) {
            $this->namingConvention = $keyValueStore->get('namingConvention');
        } else {
            $this->namingConvention = 'id';
        }

        if ($keyValueStore->exists('anneeCreation')) {
            $this->anneeCreation = intval($keyValueStore->get('anneeCreation'));
        } else {
            throw new \LogicException('Parameter Année Creation is undefined.');
        }

        if ($keyValueStore->exists('fraisDossierDefaut')) {
            $this->defaultFraisDossier = $keyValueStore->get('fraisDossierDefaut');
        } else {
            throw new \LogicException('Parameter Frais Dossier Defaut is undefined.');
        }

        if ($keyValueStore->exists('pourcentageAcompteDefaut')) {
            $this->defaultPourcentageAcompte = $keyValueStore->get('pourcentageAcompteDefaut');
        } else {
            throw new \LogicException('Parameter Pourcentage Acompte Defaut is undefined.');
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
        return [
            'getErrors' => new \Twig_Function_Method($this, 'getErrors'),
            'getWarnings' => new \Twig_Function_Method($this, 'getWarnings'),
            'getInfos' => new \Twig_Function_Method($this, 'getInfos'),
            'getEtatDoc' => new \Twig_Function_Method($this, 'getEtatDoc'),
            'getEtatFacture' => new \Twig_Function_Method($this, 'getEtatFacture'),
            'confidentielRefus' => new \Twig_Function_Method($this, 'confidentielRefusTwig'),
        ];
    }

    /***
     *
     * Juste un test
     */
    public function getFilters()
    {
        return [
            'nbsp' => new \Twig_Filter_Method($this, 'nonBreakingSpace'),
            'string' => new \Twig_Filter_Method($this, 'toString'),
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

    /**
     * @param Etude $etude
     * @param User  $user
     *
     * @return bool
     *              Comme l'authorizationChecker n'est pas dispo coté twig, on utilisera cette méthode uniquement dans les controllers.
     *              Pour twig, utiliser confidentielRefusTwig(Etude, User, is_granted('ROLE_SOUHAITE'))
     */
    public function confidentielRefus(Etude $etude, User $user)
    {
        try {
            if ($etude->getConfidentiel() && !$this->authorizationChecker->isGranted('ROLE_CA')) {
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
     * Get référence du document
     * Params : Etude $etude, mixed $doc, string $type (the type of doc).
     *
     * @param Etude $etude
     * @param $type
     * @param int $key
     *
     * @return string
     */
    public function getRefDoc(Etude $etude, $type, $key = -1)
    {
        $type = strtoupper($type);
        $name = $etude->getReference($this->namingConvention);
        if ('AP' == $type) {
            if ($etude->getAp()) {
                return $name . '-' . $type . '-' . $etude->getAp()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ('CC' == $type) {
            if ($etude->getCc()) {
                return $name . '-' . $type . '-' . $etude->getCc()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ('RM' == $type || 'DM' == $type) {
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
        } elseif ('FA' == $type) {
            return $name . '-' . $type;
        } elseif ('FI' == $type) {
            return $name . '-' . $type . ($key + 1);
        } elseif ('FS' == $type) {
            return $name . '-' . $type;
        } elseif ('PVI' == $type) {
            if ($key >= 0 && $etude->getPvis($key)) {
                return $name . '-' . $type . ($key + 1) . '-' . $etude->getPvis($key)->getVersion();
            } else {
                return $name . '-' . $type . ($key + 1) . '- ERROR GETTING PVI';
            }
        } elseif ('PVR' == $type) {
            if ($etude->getPvr()) {
                return $name . '-' . $type . '-' . $etude->getPvr()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ('CE' == $type) {
            if (!$etude->getMissions()->get($key)
                || !$etude->getMissions()->get($key)->getIntervenant()
            ) {
                return $etude->getMandat() . '-CE- ERROR GETTING DEV ID';
            } else {
                $identifiant = $etude->getMissions()->get($key)->getIntervenant()->getIdentifiant();
            }

            return $etude->getMandat() . '-CE-' . $identifiant;
        } elseif ('AVCC' == $type) {
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
     * Get frais de dossier par défaut.
     */
    public function getDefaultFraisDossier()
    {
        return $this->defaultFraisDossier;
    }

    /*
     * Get pourcentage d'acompte par défaut.
     */
    public function getDefaultPourcentageAcompte()
    {
        return $this->defaultPourcentageAcompte;
    }

    public function getDernierContact(Etude $etude)
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

    public function getErrors(Etude $etude)
    {
        $errors = [];

        /**************************************************
         * Vérification de la cohérence des dateSignature *
         **************************************************/

        // AP > CC
        if ($etude->getAp() && $etude->getCc()) {
            if (null !== $etude->getCc()->getDateSignature() && $etude->getAp()->getDateSignature() > $etude->getCc()->getDateSignature()) {
                $error = ['titre' => 'AP, CC - Date de signature : ', 'message' => 'La date de signature de l\'Avant Projet doit être antérieure ou égale à la date de signature de la Convention Client.'];
                array_push($errors, $error);
            }
        }

        // CC > RM
        if ($etude->getCc()) {
            foreach ($etude->getMissions() as $mission) {
                if (null !== $mission->getDateSignature() && $etude->getCc()->getDateSignature() > $mission->getDateSignature()) {
                    $error = ['titre' => 'RM, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure ou égale à la date de signature des récapitulatifs de mission.'];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // CC > PVI
        if ($etude->getCc()) {
            foreach ($etude->getPvis() as $pvi) {
                if (null !== $pvi->getDateSignature() && $etude->getCc()->getDateSignature() >= $pvi->getDateSignature()) {
                    $error = ['titre' => 'PVIS, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure à la date de signature des PVIS.'];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        // CC > FI
        if ($etude->getCc()) {
            foreach ($etude->getFactures() as $FactureVente) {
                if (null !== $FactureVente->getDateEmission() && $etude->getCc()->getDateSignature() > $FactureVente->getDateEmission()) {
                    $error = ['titre' => 'Factures, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure à la date de signature des Factures.'];
                    array_push($errors, $error);
                    break;
                }
            }
        }

        //ordre PVI
        foreach ($etude->getPvis() as $pvi) {
            if (isset($pviAnterieur)) {
                if (null !== $pvi->getDateSignature() && $pvi->getDateSignature() < $pviAnterieur->getDateSignature()) {
                    $error = ['titre' => 'PVIS - Date de signature : ', 'message' => 'La date de signature du PVI1 doit être antérieure à celle du PVI2 et ainsi de suite.
           '];
                    array_push($errors, $error);
                    break;
                }
            }
            $pviAnterieur = $pvi;
        }

        // PVR < fin d'étude
        if ($etude->getPvr()) {
            if (null !== $etude->getDateFin(true) && $etude->getPvr()->getDateSignature() > $etude->getDateFin(true)) {
                $error = ['titre' => 'PVR  - Date de signature : ', 'message' => 'La date de signature du PVR doit être antérieure à la date de fin de l\'étude. Consulter la Convention Client ou l\'Avenant à la Convention Client pour la fin l\'étude.'];
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

                $error = ['titre' => 'CE - RM : ' . $intervenant->getPersonne()->getPrenomNom(), 'message' => 'La date de signature de la Convention Eleve de ' . $intervenant->getPersonne()->getPrenomNom() . ' doit être antérieure à la date de signature du récapitulatifs de mission.'];
                $errorAbs = ['titre' => 'CE - RM : ' . $intervenant->getPersonne()->getPrenomNom(), 'message' => 'La Convention Eleve de ' . $intervenant->getPersonne()->getPrenomNom() . ' n\'est pas signée.'];

                if (null === $intervenant->getDateConventionEleve()) {
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
                    $error = ['titre' => 'Fin de l\'étude :', 'message' => 'L\'étude se termine dans moins de dix jours, pensez à faire signer le PVR ou à faire signer des avenants de délais si vous pensez que l\'étude ne se terminera pas à temps.'];
                    array_push($errors, $error);
                } elseif ($etude->getDateFin(true) < $now) {
                    $error = ['titre' => 'Fin de l\'étude :', 'message' => 'La fin de l\'étude est passée. Pensez à faire un PVR ou des avenants à la CC et au(x) RM.'];
                    array_push($errors, $error);
                }
            } else {
                if ($etude->getPvr()->getDateSignature() > $etude->getDateFin(true)) {
                    $error = ['titre' => 'Fin de l\'étude :', 'message' => 'La date du PVR est située après la fin de l\'étude.'];
                    array_push($errors, $error);
                }
            }
        }

        /*************************
         * Contenu des documents *
         *************************/

        // Description de l'AP suffisante
        if (strlen($etude->getDescriptionPrestation()) < 300) {
            $error = ['titre' => 'Description de l\'étude:', 'message' => 'Attention la description de l\'étude dans l\'AP fait moins de 300 caractères'];
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
            $error = ['titre' => 'Incohérence dans les JEH reversé', 'message' => "Vous reversez plus de JEH ($jehReverses) que vous n'en n'avez facturé ($jehFactures)"];
            array_push($errors, $error);
        }

        /*****************************************************
         * Vérification de la nationnalité des intervenants  *
         *****************************************************/
        foreach ($etude->getMissions() as $mission) {
            // Vérification de la présence d'intervenant algériens
            $intervenant = $mission->getIntervenant();
            if ($intervenant && 'DZ' == $intervenant->getNationalite()) {
                $error = ['titre' => 'Nationalité des Intervenants', 'message' => "L'intervenant " . $intervenant->getPersonne()->getPrenomNom() . " est de nationnalité algériennne. Il ne peut intervenir sur l'étude."];
                array_push($errors, $error);
            }
        }

        /*
         * Verification que les dates de debut de phases correspondent bien avec la date de signature de la CC
         * On créé juste un compteur d'erreur pour ne pas spammer l'utilisateur sous un grand nombre d'erreurs liées juste aux phases.
         */
        $phasesErreurDate = 0; //compteur des phases avec date incorrectes
        if (null !== $etude->getCc()) {
            foreach ($etude->getPhases() as $phase) {
                if ($phase->getDateDebut() < $etude->getCc()->getDateSignature()) {
                    ++$phasesErreurDate;
                }
            }
            if ($phasesErreurDate > 0) {
                $error = ['titre' => 'Date des phases', 'message' => 'Il y a ' . $phasesErreurDate . ' erreur(s) dans les dates de début de phases.'];
                array_push($errors, $error);
            }
        }

        return $errors;
    }

    public function getWarnings(Etude $etude)
    {
        $warnings = [];

        // Description de l'AP insuffisante
        $length = strlen($etude->getDescriptionPrestation());
        if ($length > 300 && $length < 500) {
            $error = ['titre' => 'Description de l\'étude:', 'message' => 'Attention la description de l\'étude dans l\'AP fait moins de 500 caractères'];
            array_push($warnings, $error);
        }

        // Entité sociale absente
        if (null === $etude->getProspect()->getEntite()) {
            $warning = ['titre' => 'Entité sociale : ', 'message' => 'L\'entité sociale est absente. Vérifiez bien que la société est bien enregistrée et toujours en activité.'];
            array_push($warnings, $warning);
        }

        // Etude se termine dans 20 jours
        $now = new \DateTime('now');
        $DateAvert0 = new \DateInterval('P20D');
        $DateAvert1 = new \DateInterval('P10D');
        if ($etude->getDateFin()) {
            if ($etude->getDateFin()->sub($DateAvert1) > $now && $etude->getDateFin()->sub($DateAvert0) < $now) {
                $warning = ['titre' => 'Fin de l\'étude :', 'message' => 'l\'étude se termine dans moins de vingt jours, pensez à faire signer le PVR ou à faire signer des avenants de délais si vous pensez que l\'étude ne se terminera pas à temps.'];
                array_push($warnings, $warning);
            }
        }

        // Date RM Mal renseignée
        // CE + 1w < RM
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
                    $warning = ['titre' => 'Dates sur le RM de ' . $intervenant->getPersonne()->getPrenomNom(), 'message' => 'Le RM de ' . $intervenant->getPersonne()->getPrenomNom() . ' est mal rédigé. Vérifiez les dates de signature et de début de mission.'];
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
            if ($intervenant && 'FR' != $intervenant->getNationalite() && 'DZ' != $intervenant->getNationalite()) {
                $warning = ['titre' => 'Nationalité des Intervenants', 'message' => "L'intervenant " . $intervenant->getPersonne()->getPrenomNom() . " n'est pas de nationalité Française. Pensez à faire une Déclaration d'Emploi pour un étudiant Etranger auprès de la préfecture."];
                array_push($warnings, $warning);
            }
        }

        return $warnings;
    }

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
                    $info = ['titre' => 'Convention Client : ', 'message' => 'à faire relire par le Responsable Qualité'];
                    array_push($infos, $info);
                } elseif (!$etude->getAp()->getSpt1()) {
                    $info = ['titre' => 'Convention Client : ', 'message' => 'à faire signer par le signer par le président'];
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
                    $info = ['titre' => 'Récapitulatif de mission : ', 'message' => 'à faire relire par le responsable qualité'];
                    array_push($infos, $info);
                    break;
                } elseif (!$mission->getSpt1() || !$mission->getSpt2()) {
                    if (!$mission->getSpt1()) {
                        $info = ['titre' => 'Récapitulatif de mission : ', 'message' => 'à faire signer, parapher et tamponner par le président'];
                        array_push($infos, $info);
                    }

                    if (!$mission->getSpt2()) {
                        $info = ['titre' => 'Récapitulatif de mission : ', 'message' => 'à faire signer par l\'intervenant'];
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
    // n'est pas pertinent. On ne teste donc que l'existence et loe versement.

    /**
     * @param $doc
     *
     * @return $ok : 0=> null, 1 => emis, 2=>recu
     */
    public function getEtatFacture($doc)
    {
        if (null !== $doc) {
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
        return strval($this->anneeCreation + $idMandat) . '/' . strval($this->anneeCreation + 1 + $idMandat);
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
     *
     * @param \DateTime $date
     *
     * @return int
     */
    public function dateToMandat(\DateTime $date)
    {
        $interval = new \DateInterval('P2M20D');
        $date2 = clone $date;
        $date2->sub($interval);

        return intval($date2->format('Y')) - $this->anneeCreation;
    }

    /**
     * Taux de conversion.
     */
    public function getTauxConversion()
    {
        $tauxConversion = [];

        //recup toute les etudes
        $etudes = $this->em->getRepository('MgateSuiviBundle:Etude')->findAll();
        foreach ($etudes as $etude) {
            $mandat = $etude->getMandat();
            if (null !== $etude->getAp()) {
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
                    $tauxConversionCalc = ['mandat' => $mandat, 'ap_redige' => $ApRedige, 'ap_signe' => $ApSigne];
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
                    $tauxConversionCalc = ['mandat' => $mandat, 'ap_redige' => $ApRedige, 'ap_signe' => $ApSigne];
                    $tauxConversion[$mandat] = $tauxConversionCalc;
                }
            }
        }

        return $tauxConversion;
    }
}
