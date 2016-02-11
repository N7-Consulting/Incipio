<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\SuiviBundle\Entity\Etude.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\EtudeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Etude extends \Symfony\Component\DependencyInjection\ContainerAware
{
    /************************
     *    ORM DEFINITIONS
     ************************
     * Primitive definitions
     ************************/

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @Assert\GreaterThan(value = 1966)
     * Mandat > 1967 => mandat n'est pas utilisé pour compter les mandats depuis le début de la JE, mais comme l'année de prise de fonction.
     * On n'utilise pas anneeCreation de parameters.yml pour limiter les effets de bords.
     * @ORM\Column(name="mandat", type="integer")
     */
    private $mandat;

    /**
     * @var int
     *
     * @ORM\Column(name="num", type="integer", nullable=true, unique=true)
     */
    private $num;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false,  unique=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModification", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $dateModification;

    /**
     * @var int
     *
     * @ORM\Column(name="stateID", type="integer", nullable=true)
     */
    private $stateID;

    /**
     * @var string
     *
     * @ORM\Column(name="stateDescription", type="text", nullable=true)
     */
    private $stateDescription;

    /**
     * @var bool
     *
     * @ORM\Column(name="confidentiel", type="boolean", nullable=true)
     */
    private $confidentiel;

    /**
     * @var string
     *
     * @ORM\Column(name="competences", type="text", nullable=true)
     */
    private $competences;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="auditDate", type="date", nullable=true)
     */
    private $auditDate;

    /**
     * @var string
     *
     * @ORM\Column(name="auditType", type="integer", nullable=true)
     */
    private $auditType;

    /************************
     *    ORM DEFINITIONS
     ************************
     *    Relationships
     ************************/

    /**
     * @ORM\OneToMany(targetEntity="mgate\PubliBundle\Entity\RelatedDocument", mappedBy="etude", cascade={"remove"})
     */
    private $relatedDocuments;

    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Prospect", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $prospect;

    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $suiveur;

    /**
     * @ORM\OneToOne(targetEntity="\mgate\CommentBundle\Entity\Thread", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $thread;

    /**
     * @ORM\OneToMany(targetEntity="ClientContact", mappedBy="etude", cascade={"persist", "remove"})
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $clientContacts;

    /**
     * @ORM\OneToMany(targetEntity="Suivi", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $suivis;

    /**
     * @ORM\OneToOne(targetEntity="Ap", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $ap;

    /**
     * @ORM\OneToOne(targetEntity="Cc", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $cc;

    /**
     * @ORM\OneToMany(targetEntity="mgate\TresoBundle\Entity\Facture", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $factures;

    /**
     * @ORM\OneToMany(targetEntity="ProcesVerbal", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $procesVerbaux;

    /**
     * @ORM\OneToMany(targetEntity="Av", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $avs;

    /**
     * @ORM\OneToMany(targetEntity="GroupePhases", mappedBy="etude", cascade={"persist", "remove"})
     * @ORM\OrderBy({"numero" = "ASC"})
     */
    private $groupes;

    /**
     * @ORM\OneToMany(targetEntity="Phase", mappedBy="etude", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $phases;

    /**
     * @ORM\OneToMany(targetEntity="Mission", mappedBy="etude", cascade={"persist","remove"})
     */
    private $missions;

    /**
     * @ORM\OneToMany(targetEntity="AvMission", mappedBy="etude")
     */
    private $avMissions;

    /**
     * @ORM\ManyToOne(targetEntity="DomaineCompetence", inversedBy="etude")
     * @ORM\JoinColumn(nullable=true)
     */
    private $domaineCompetence;

    /**
     * @var bool
     *
     * @ORM\Column(name="acompte", type="boolean", nullable=true)
     */
    private $acompte;

    /**
     * @var int
     *
     * @ORM\Column(name="pourcentageAcompte", type="decimal", scale=2, nullable=true)
     */
    private $pourcentageAcompte;

    /**
     * @var int
     *
     * @ORM\Column(name="fraisDossier", type="integer", nullable=true)
     */
    private $fraisDossier;

    /**
     * @var text
     *
     * @ORM\Column(name="presentationProjet", type="text", nullable=true)
     */
    private $presentationProjet;

    /**
     * @var text
     *
     * @ORM\Column(name="descriptionPrestation", type="text", nullable=true)
     */
    private $descriptionPrestation;

    /**
     * @var text
     *
     * @ORM\Column(name="prestation", type="integer", nullable=true)
     */
    private $typePrestation;

    /**
     * @var int
     *
     * @ORM\Column(name="sourceDeProspection", type="integer", nullable=true)
     */
    private $sourceDeProspection;

    /************************
     *   OTHERS DEFINITIONS
     ************************/

    /**
     * @var bool
     */
    private $knownProspect = false;

    /**
     * @var bool
     */
    private $newProspect;

    /**
     * ADDITIONAL GETTERS/SETTERS.
     */
    public function getReference()
    {
       // return (string) ($this->getMandat() * 100 + $this->getNum());
        return $this->getNom();
    }

    public function getFa()
    {
        foreach ($this->factures as $facture) {
            if ($facture->getType() == \mgate\TresoBundle\Entity\Facture::$TYPE_VENTE_ACCOMPTE) {
                return $facture;
            }
        }

        return;
    }

    public function getFs()
    {
        foreach ($this->factures as $facture) {
            if ($facture->getType() == \mgate\TresoBundle\Entity\Facture::$TYPE_VENTE_SOLDE) {
                return $facture;
            }
        }

        return;
    }

    public function getNumero()
    {
        return $this->mandat * 100 + $this->num;
    }

    public function getMontantJEHHT()
    {
        $total = 0;
        foreach ($this->phases as $phase) {
            $total += $phase->getNbrJEH() * $phase->getPrixJEH();
        }

        return $total;
    }

    public function getMontantHT()
    {
        return $this->fraisDossier + $this->getMontantJEHHT();
    }

    public function getNbrJEH()
    {
        $total = 0;
        foreach ($this->phases as $phase) {
            $total += $phase->getNbrJEH();
        }

        return $total;
    }

    /**
     * Renvoie la date de lancement Réel (Signature CC) ou Théorique (Début de la phase la plus en amont).
     *
     * @return DateTime
     */
    public function getDateLancement()
    {
        if ($this->cc) { // Réel
            return $this->cc->getDateSignature();
        } else { // Théorique
            $dateDebut = array();
            $phases = $this->phases;
            foreach ($phases as $phase) {
                if ($phase->getDateDebut() != null) {
                    array_push($dateDebut, $phase->getDateDebut());
                }
            }

            if (count($dateDebut) > 0) {
                return min($dateDebut);
            } else {
                return;
            }
        }
    }

    /**
     * Renvoie la date de fin : Fin de la phase la plus en aval.
     *
     * @return DateTime
     */
    public function getDateFin($avecAvenant = false)
    {
        $dateFin = array();
        $phases = $this->phases;

        foreach ($phases as $p) {
            if ($p->getDateDebut() != null && $p->getDelai() != null) {
                $dateDebut = clone $p->getDateDebut(); //WARN $a = $b : $a pointe vers le même objet que $b...
                array_push($dateFin, $dateDebut->modify('+'.$p->getDelai().' day'));
                unset($dateDebut);
            }
        }

        if (count($dateFin) > 0) {
            $dateFin = max($dateFin);
            if ($avecAvenant && $this->avs && $this->avs->last()) {
                $dateFin->modify('+'.$this->avs->last()->getDifferentielDelai().' day');
            }

            return $dateFin;
        } else {
            return;
        }
    }

    public function getDelai($avecAvenant = false)
    {
        if ($this->getDateFin($avecAvenant)) {
            if ($this->cc && $this->cc->getDateSignature()) { // Réel
                return $this->getDateFin($avecAvenant)->diff($this->cc->getDateSignature());
            } elseif ($this->getDateLancement()) {
                return $this->getDateFin($avecAvenant)->diff($this->getDateLancement());
            }
        }

        return;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->relatedDocuments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clientContacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->suivis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phases = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->missions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->factures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->procesVerbaux = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avMissions = new \Doctrine\Common\Collections\ArrayCollection();

        $this->fraisDossier = 90;
        $this->pourcentageAcompte = 0.40;
        $this->stateID = 1;
    }

    /**
     * @deprecated since 0 0
     */
    public function getDoc($doc, $key = -1)
    {
        switch (strtoupper($doc)) {
            case 'AP':
                return $this->getAp();
            case 'CC':
                return $this->getCc();
            case 'FA':
                return $this->getFa();
            case 'FI':
                return $this->getFis($key);
            case 'FS':
                return $this->getFs();
            case 'PVR':
                return $this->getPvr();
            case 'PVI':
                return $this->getPvis($key);
            case 'AV':
                return $this->getAvs()->get($key);
            case 'RM':
                if ($key == -1) {
                    return;
                } else {
                    return $this->getMissions()->get($key);
                }
            default:
                return;
        }
    }

    /**
     * AUTO GENERATED GETTER/SETTER.
     */

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateCreation.
     *
     * @param \DateTime $dateCreation
     *
     * @return Etude
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation.
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateModification.
     *
     * @param \DateTime $dateModification
     *
     * @return Etude
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification.
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set mandat.
     *
     * @param int $mandat
     *
     * @return Etude
     */
    public function setMandat($mandat)
    {
        $this->mandat = $mandat;

        return $this;
    }

    /**
     * Get mandat.
     *
     * @return int
     */
    public function getMandat()
    {
        return $this->mandat;
    }

    /**
     * Set num.
     *
     * @param int $num
     *
     * @return Etude
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num.
     *
     * @return int
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Etude
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Etude
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set competences.
     *
     * @param string $competences
     *
     * @return Etude
     */
    public function setCompetences($competences)
    {
        $this->competences = $competences;

        return $this;
    }

    /**
     * Get competences.
     *
     * @return string
     */
    public function getCompetences()
    {
        return $this->competences;
    }

    /**
     * Set auditDate.
     *
     * @param \DateTime $auditDate
     *
     * @return Etude
     */
    public function setAuditDate($auditDate)
    {
        $this->auditDate = $auditDate;

        return $this;
    }

    /**
     * Get auditDate.
     *
     * @return \DateTime
     */
    public function getAuditDate()
    {
        return $this->auditDate;
    }

    /**
     * Set auditType.
     *
     * @param string $auditType
     *
     * @return Etude
     */
    public function setAuditType($auditType)
    {
        $this->auditType = $auditType;

        return $this;
    }

    /**
     * Get audit.
     *
     * @return string
     */
    public function getAuditType()
    {
        return $this->auditType;
    }

    public static function getAuditTypeChoice()
    {
        return array('1' => 'Déontologique',
            '2' => 'Exhaustif', );
    }

    public static function getAuditTypeChoiceAssert()
    {
        return array_keys(self::getAuditTypeChoice());
    }

    public function getAuditTypeToString()
    {
        $tab = $this->getAuditTypeChoice();

        return $tab[$this->auditType];
    }

    /**
     * Set acompte.
     *
     * @param bool $acompte
     *
     * @return Etude
     */
    public function setAcompte($acompte)
    {
        $this->acompte = $acompte;

        return $this;
    }

    /**
     * Get acompte.
     *
     * @return bool
     */
    public function getAcompte()
    {
        return $this->acompte;
    }

    /**
     * Set pourcentageAcompte.
     *
     * @param int $pourcentageAcompte
     *
     * @return Etude
     */
    public function setPourcentageAcompte($pourcentageAcompte)
    {
        $this->pourcentageAcompte = $pourcentageAcompte;

        return $this;
    }

    /**
     * Get pourcentageAcompte.
     *
     * @return int
     */
    public function getPourcentageAcompte()
    {
        return $this->pourcentageAcompte;
    }

    /**
     * Set fraisDossier.
     *
     * @param int $fraisDossier
     *
     * @return Etude
     */
    public function setFraisDossier($fraisDossier)
    {
        $this->fraisDossier = $fraisDossier;

        return $this;
    }

    /**
     * Get fraisDossier.
     *
     * @return int
     */
    public function getFraisDossier()
    {
        return $this->fraisDossier;
    }

    /**
     * Set presentationProjet.
     *
     * @param string $presentationProjet
     *
     * @return Etude
     */
    public function setPresentationProjet($presentationProjet)
    {
        $this->presentationProjet = $presentationProjet;

        return $this;
    }

    /**
     * Get presentationProjet.
     *
     * @return string
     */
    public function getPresentationProjet()
    {
        return $this->presentationProjet;
    }

    /**
     * Set descriptionPrestation.
     *
     * @param string $descriptionPrestation
     *
     * @return Etude
     */
    public function setDescriptionPrestation($descriptionPrestation)
    {
        $this->descriptionPrestation = $descriptionPrestation;

        return $this;
    }

    /**
     * Get descriptionPrestation.
     *
     * @return string
     */
    public function getDescriptionPrestation()
    {
        return $this->descriptionPrestation;
    }

    /**
     * Set typePrestation.
     *
     * @param string $typePrestation
     *
     * @return Etude
     */
    public function setTypePrestation($typePrestation)
    {
        $this->typePrestation = $typePrestation;

        return $this;
    }

    /**
     * Get typePrestation.
     *
     * @return string
     */
    public function getTypePrestation()
    {
        return $this->typePrestation;
    }

    public static function getTypePrestationChoice()
    {
        return array('1' => 'ingénieur Info',
            '2' => 'ingénieur EN',
            '3' => 'ingénieur TR',
            '4' => 'ingénieur GEA',
            '5'=> 'ingénieur Hydro');
    }

    public static function getTypePrestationChoiceAssert()
    {
        return array_keys(self::getTypePrestationChoice());
    }

    public function getTypePrestationToString()
    {
        if ($this->typePrestation) {
            $tab = $this->getTypePrestationChoice();

            return $tab[$this->typePrestation];
        } else {
            return;
        }
    }

    /**
     * Set prospect.
     *
     * @param \mgate\PersonneBundle\Entity\Prospect $prospect
     *
     * @return Etude
     */
    public function setProspect(\mgate\PersonneBundle\Entity\Prospect $prospect)
    {
        $this->prospect = $prospect;

        return $this;
    }

    /**
     * Get prospect.
     *
     * @return \mgate\PersonneBundle\Entity\Prospect
     */
    public function getProspect()
    {
        return $this->prospect;
    }

    /**
     * Set suiveur.
     *
     * @param \mgate\PersonneBundle\Entity\Personne $suiveur
     *
     * @return Etude
     */
    public function setSuiveur(\mgate\PersonneBundle\Entity\Personne $suiveur = null)
    {
        $this->suiveur = $suiveur;

        return $this;
    }

    /**
     * Get suiveur.
     *
     * @return \mgate\PersonneBundle\Entity\Personne
     */
    public function getSuiveur()
    {
        return $this->suiveur;
    }

    /**
     * Add clientContacts.
     *
     * @param \mgate\SuiviBundle\Entity\ClientContact $clientContacts
     *
     * @return Etude
     */
    public function addClientContact(\mgate\SuiviBundle\Entity\ClientContact $clientContacts)
    {
        $this->clientContacts[] = $clientContacts;

        return $this;
    }

    /**
     * Remove clientContacts.
     *
     * @param \mgate\SuiviBundle\Entity\ClientContact $clientContacts
     */
    public function removeClientContact(\mgate\SuiviBundle\Entity\ClientContact $clientContacts)
    {
        $this->clientContacts->removeElement($clientContacts);
    }

    /**
     * Get clientContacts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientContacts()
    {
        return $this->clientContacts;
    }

    /**
     * Add suivi.
     *
     * @param \mgate\SuiviBundle\Entity\Suivi $suivi
     *
     * @return Etude
     */
    public function addSuivi(\mgate\SuiviBundle\Entity\Suivi $suivi)
    {
        $this->suivis[] = $suivi;

        return $this;
    }

    /**
     * Remove suivi.
     *
     * @param \mgate\SuiviBundle\Entity\Suivi $suivi
     */
    public function removeSuivi(\mgate\SuiviBundle\Entity\Suivi $suivi)
    {
        $this->suivis->removeElement($suivi);
    }

    /**
     * Get suivis.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSuivis()
    {
        return $this->suivis;
    }

    /**
     * Set ap.
     *
     * @param \mgate\SuiviBundle\Entity\Ap $ap
     *
     * @return Etude
     */
    public function setAp(\mgate\SuiviBundle\Entity\Ap $ap = null)
    {
        if ($ap != null) {
            $ap->setEtude($this);
        }

        $this->ap = $ap;

        return $this;
    }

    /**
     * Get ap.
     *
     * @return \mgate\SuiviBundle\Entity\Ap
     */
    public function getAp()
    {
        return $this->ap;
    }

    /**
     * Add phases.
     *
     * @param \mgate\SuiviBundle\Entity\Phase $phases
     *
     * @return Etude
     */
    public function addPhase(\mgate\SuiviBundle\Entity\Phase $phases)
    {
        $this->phases[] = $phases;

        return $this;
    }

    /**
     * Remove phases.
     *
     * @param \mgate\SuiviBundle\Entity\Phase $phases
     */
    public function removePhase(\mgate\SuiviBundle\Entity\Phase $phases)
    {
        $this->phases->removeElement($phases);
    }

    /**
     * Get phases.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * Set cc.
     *
     * @param \mgate\SuiviBundle\Entity\Cc $cc
     *
     * @return Etude
     */
    public function setCc(\mgate\SuiviBundle\Entity\Cc $cc = null)
    {
        if ($cc != null) {
            $cc->setEtude($this);
        }

        $this->cc = $cc;

        return $this;
    }

    /**
     * Get cc.
     *
     * @return \mgate\SuiviBundle\Entity\Cc
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Add mission.
     *
     * @param \mgate\SuiviBundle\Entity\Mission $mission
     *
     * @return Etude
     */
    public function addMission(\mgate\SuiviBundle\Entity\Mission $mission)
    {
        $this->missions[] = $mission;

        return $this;
    }

    /**
     * Remove missions.
     *
     * @param \mgate\SuiviBundle\Entity\Mission $missions
     */
    public function removeMission(\mgate\SuiviBundle\Entity\Mission $missions)
    {
        $this->missions->removeElement($missions);
    }

    /**
     * Get missions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * Add Facture.
     *
     * @param \mgate\TresoBundle\Entity\Facture $facture
     *
     * @return Etude
     */
    public function addFacture(\mgate\TresoBundle\Entity\Facture $facture)
    {
        $this->factures[] = $facture;

        return $this;
    }

    /**
     * Remove Facture.
     *
     * @param \mgate\TresoBundle\Entity\Facture $facture
     */
    public function removeFacture(\mgate\TresoBundle\Entity\Facture $facture)
    {
        $this->factures->removeElement($facture);
    }

    /**
     * Get factures.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFactures()
    {
        return $this->factures;
    }

    /**
     * Remove procesVerbal.
     *
     * @param \mgate\SuiviBundle\Entity\ProcesVerbal $facture
     */
    public function removeProcesVerbal(\mgate\SuiviBundle\Entity\ProcesVerbal $pv)
    {
        $this->procesVerbaux->removeElement($pv);
    }

    /**
     * Get factures.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProcesVerbaux()
    {
        return $this->procesVerbaux;
    }

    /**
     * Add pvis.
     *
     * @param \mgate\SuiviBundle\Entity\ProcesVerbal $pvi
     *
     * @return Etude
     */
    public function addPvi(\mgate\SuiviBundle\Entity\ProcesVerbal $pvi)
    {
        $this->procesVerbaux[] = $pvi;
        $pvi->setEtude($this);
        $pvi->setType('pvi');

        return $this;
    }

    /**
     * Remove pvis.
     *
     * @param \mgate\SuiviBundle\Entity\PvProcesVerbali $pvis
     */
    public function removePvi(\mgate\SuiviBundle\Entity\ProcesVerbal $pvis)
    {
        $this->procesVerbaux->removeElement($pvis);
    }

    /**
     * Get pvis.
     *
     * @return mixed(array, ProcesVerbal)
     */
    public function getPvis($key = -1)
    {
        $pvis = array();

        foreach ($this->procesVerbaux as $value) {
            if ($value->getType() == 'pvi') {
                $pvis[] = $value;
            }
        }

        if ($key >= 0) {
            if ($key < count($pvis)) {
                return $pvis[$key];
            } else {
                return;
            }
        }

        usort($pvis, array($this, 'trieDateSignature'));

        return $pvis;
    }

    /**
     * Add avs.
     *
     * @param \mgate\SuiviBundle\Entity\Av $avs
     *
     * @return Etude
     */
    public function addAv(\mgate\SuiviBundle\Entity\Av $avs)
    {
        $this->avs[] = $avs;

        return $this;
    }

    /**
     * Remove avs.
     *
     * @param \mgate\SuiviBundle\Entity\Av $avs
     */
    public function removeAv(\mgate\SuiviBundle\Entity\Av $avs)
    {
        $this->avs->removeElement($avs);
    }

    /**
     * Get avs.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvs()
    {
        return $this->avs;
    }

    /**
     * Add avMissions.
     *
     * @param \mgate\SuiviBundle\Entity\AvMission $avMissions
     *
     * @return Etude
     */
    public function addAvMission(\mgate\SuiviBundle\Entity\AvMission $avMissions)
    {
        $this->avMissions[] = $avMissions;

        return $this;
    }

    /**
     * Remove avMissions.
     *
     * @param \mgate\SuiviBundle\Entity\AvMission $avMissions
     */
    public function removeAvMission(\mgate\SuiviBundle\Entity\AvMission $avMissions)
    {
        $this->avMissions->removeElement($avMissions);
    }

    /**
     * Get avMissions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvMissions()
    {
        return $this->avMissions;
    }

    /**
     * Set pvr.
     *
     * @param \mgate\SuiviBundle\Entity\ProcesVerbal $pvr
     *
     * @return Etude
     */
    public function setPvr(\mgate\SuiviBundle\Entity\ProcesVerbal $pvr)
    {
        $pvr->setEtude($this);
        $pvr->setType('pvr');

        foreach ($this->procesVerbaux as $pv) {
            if ($pv->getType() == 'pvr') {
                $pv = $pvr;

                return $this;
            }
        }
        $this->procesVerbaux[] = $pvr;
    }

    /**
     * Get pvr.
     *
     * @return \mgate\SuiviBundle\Entity\ProcesVerbal
     */
    public function getPvr()
    {
        foreach ($this->procesVerbaux as $pv) {
            if ($pv->getType() == 'pvr') {
                return $pv;
            }
        }
    }

    /**
     * Set thread.
     *
     * @param \mgate\CommentBundle\Entity\Thread $thread
     *
     * @return Prospect
     */
    public function setThread(\mgate\CommentBundle\Entity\Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread.
     *
     * @return mgate\CommentBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set stateID.
     *
     * @param int $stateID
     *
     * @return Etude
     */
    public function setStateID($stateID)
    {
        $this->stateID = $stateID;

        return $this;
    }

    /**
     * Get stateID.
     *
     * @return int
     */
    public function getStateID()
    {
        return $this->stateID;
    }

    public static function getStateIDChoice()
    {
        return array('1' => 'En négociation',
            '2' => 'En cours',
            '3' => 'En pause',
            '4' => 'Cloturée',
            '5' => 'Avortée', );
    }

    public static function getStateIDChoiceAssert()
    {
        return array_keys(self::getStateIDChoice());
    }

    public function getStateIDToString()
    {
        $tab = $this->getStateIDChoice();

        return $this->stateID ? $tab[$this->stateID] : '';
    }

    /**
     * Set stateDescription.
     *
     * @param string $stateDescription
     *
     * @return Etude
     */
    public function setStateDescription($stateDescription)
    {
        $this->stateDescription = $stateDescription;

        return $this;
    }

    /**
     * Get stateDescription.
     *
     * @return string
     */
    public function getStateDescription()
    {
        return $this->stateDescription;
    }

    /**
     * Set confidentiel.
     *
     * @param bool $confidentiel
     *
     * @return Etude
     */
    public function setConfidentiel($confidentiel)
    {
        $this->confidentiel = $confidentiel;

        return $this;
    }

    /**
     * Get confidentiel.
     *
     * @return bool
     */
    public function getConfidentiel()
    {
        return $this->confidentiel;
    }

    /**
     * Add groupes.
     *
     * @param \mgate\SuiviBundle\Entity\GroupePhases $groupes
     *
     * @return Etude
     */
    public function addGroupe(\mgate\SuiviBundle\Entity\GroupePhases $groupe)
    {
        $this->groupes[] = $groupe;

        return $this;
    }

    /**
     * Remove groupes.
     *
     * @param \mgate\SuiviBundle\Entity\GroupePhases $groupes
     */
    public function removeGroupe(\mgate\SuiviBundle\Entity\GroupePhases $groupe)
    {
        $this->groupes->removeElement($groupe);
    }

    /**
     * Get groupes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    /**
     * Set sourceDeProspection.
     *
     * @param int $sourceDeProspection
     *
     * @return Etude
     */
    public function setSourceDeProspection($sourceDeProspection)
    {
        $this->sourceDeProspection = $sourceDeProspection;

        return $this;
    }

    /**
     * Get sourceDeProspection.
     *
     * @return int
     */
    public function getSourceDeProspection()
    {
        return $this->sourceDeProspection;
    }

    /**
     * Get sourceDeProspectionChoice.
     *
     * @return array
     */
    public static function getSourceDeProspectionChoice()
    {
        return array(
            1 => 'Kiwi',
            2 => 'Etude avec l\'Ecole',
            3 => 'Relation école (EPRD, Incubateur, Direction...)',
            4 => 'Participation aux évènements',
            5 => 'Réseaux des Anciens',
            6 => 'Réseaux des élèves',
            7 => 'Contact spontané',
            8 => 'Ancien client',
            9 => 'Dev\'Co N7C',
            10 => 'Autre',
            );
    }

    public function getSourceDeProspectionToString()
    {
        $tab = $this->getSourceDeProspectionChoice();

        return $this->sourceDeProspection ? $tab[$this->sourceDeProspection] : '';
    }

    /**
     * Add procesVerbaux.
     *
     * @param \mgate\SuiviBundle\Entity\ProcesVerbal $procesVerbaux
     *
     * @return Etude
     */
    public function addProcesVerbaux(\mgate\SuiviBundle\Entity\ProcesVerbal $procesVerbaux)
    {
        $this->procesVerbaux[] = $procesVerbaux;

        return $this;
    }

    /**
     * Remove procesVerbaux.
     *
     * @param \mgate\SuiviBundle\Entity\ProcesVerbal $procesVerbaux
     */
    public function removeProcesVerbaux(\mgate\SuiviBundle\Entity\ProcesVerbal $procesVerbaux)
    {
        $this->procesVerbaux->removeElement($procesVerbaux);
    }

    private function trieDateSignature($a, $b)
    {
        if ($a->getDateSignature() == $b->getDateSignature()) {
            return 0;
        } else {
            return ($a->getDateSignature() < $b->getDateSignature()) ? -1 : 1;
        }
    }

    /**
     * Add relatedDocuments.
     *
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments
     *
     * @return Etude
     */
    public function addRelatedDocument(\mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments[] = $relatedDocuments;

        return $this;
    }

    /**
     * Remove relatedDocuments.
     *
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments
     */
    public function removeRelatedDocument(\mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments->removeElement($relatedDocuments);
    }

    /**
     * Get relatedDocuments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedDocuments()
    {
        return $this->relatedDocuments;
    }

    public function isKnownProspect()
    {
        return $this->knownProspect;
    }

    public function setKnownProspect($boolean)
    {
        $this->knownProspect = $boolean;
    }

    public function getNewProspect()
    {
        return $this->newProspect;
    }

    public function setNewProspect($var)
    {
        $this->newProspect = $var;
    }

    /**
     * Set domaineCompetence.
     *
     * @param \mgate\SuiviBundle\Entity\DomaineCompetence $domaineCompetence
     *
     * @return Etude
     */
    public function setDomaineCompetence(\mgate\SuiviBundle\Entity\DomaineCompetence $domaineCompetence = null)
    {
        $this->domaineCompetence = $domaineCompetence;

        return $this;
    }

    /**
     * Get domaineCompetence.
     *
     * @return \mgate\SuiviBundle\Entity\DomaineCompetence
     */
    public function getDomaineCompetence()
    {
        return $this->domaineCompetence;
    }
}
