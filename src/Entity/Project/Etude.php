<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Project;

use App\Entity\Comment\Thread;
use App\Entity\Hr\Competence;
use App\Entity\Personne\Personne;
use App\Entity\Personne\Prospect;
use App\Entity\Publish\RelatedDocument;
use App\Entity\Treso\Facture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Project\EtudeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("num")
 * @UniqueEntity("nom")
 */
class Etude
{
    public const ETUDE_STATE_NEGOCIATION = 1;

    public const ETUDE_STATE_COURS = 2;

    public const ETUDE_STATE_PAUSE = 3;

    public const ETUDE_STATE_CLOTUREE = 4;

    public const ETUDE_STATE_AVORTEE = 5;

    // Etude non clôturée (entre en cours et clôturée)
    public const ETUDE_STATE_FINIE = 6;

    // Etude non commencée (entre en négociation et en cours)
    public const ETUDE_STATE_ACCEPTEE = 7;

    public const ETUDE_STATE_ARRAY = [
        self::ETUDE_STATE_NEGOCIATION => 'suivi.en_negociation',
        self::ETUDE_STATE_ACCEPTEE => 'suivi.acceptee',
        self::ETUDE_STATE_COURS => 'suivi.en_cours',
        self::ETUDE_STATE_FINIE => 'suivi.finie',
        self::ETUDE_STATE_CLOTUREE => 'suivi.cloturee',
        self::ETUDE_STATE_PAUSE => 'suivi.en_pause',
        self::ETUDE_STATE_AVORTEE => 'suivi.avortee',
    ];

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
     * @Assert\NotBlank()
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
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/\//", match=false)
     *
     * @Groups({"gdpr"})
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
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModification", type="datetime")
     */
    private $dateModification;

    /**
     * @var int
     * @Assert\Choice({1,2,3,4,5,6,7})
     * @ORM\Column(name="stateID", type="integer", nullable=false)
     */
    private $stateID;

    /**
     * @var string
     *
     * @ORM\Column(name="stateDescription", type="text", nullable=true)
     */
    private $stateDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="auditCommentaires", type="text", nullable=true)
     */
    private $auditCommentaires;

    /**
     * @var bool
     *
     * @ORM\Column(name="confidentiel", type="boolean", nullable=true)
     */
    private $confidentiel;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Hr\Competence", mappedBy="etudes", cascade={"persist","merge"})
     * @ORM\JoinColumn(nullable=true)
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

    /**
     * @var bool est-ce que l'étude utilise la CE ? Ce booléen sert à forcer
     *           l'affichage de la CC et de l'AP si définit à false ou null
     *
     * @ORM\Column(name="ceActive", type="boolean", nullable=true)
    */
    private $ceActive;

    /************************
     *    ORM DEFINITIONS
     ************************
     *    Relationships
     ************************/

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Publish\RelatedDocument", mappedBy="etude", cascade={"remove"})
     */
    private $relatedDocuments;

    /**
     * @Assert\Valid()
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Prospect", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $prospect;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Personne")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $suiveur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Personne")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $suiveurQualite;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Comment\Thread", cascade={"persist"})
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
     * @deprecated Only present for backward compatibility.
     * @var Ap Avant projet
     *
     * @ORM\OneToOne(targetEntity="Ap", inversedBy="etude", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $ap;

    /**
     * @deprecated Only present for backward compatibility.
     * @var Cc Convention Client
     *
     * @ORM\OneToOne(targetEntity="Cc", inversedBy="etude", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $cc;

    /**
     * @var Ce
     *
     * @ORM\OneToOne(targetEntity="Ce", inversedBy="etude", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $ce;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Treso\Facture", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $factures;

    /**
     * @var ProcesVerbal[]
     *
     * @ORM\OneToMany(targetEntity="ProcesVerbal", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $procesVerbaux;

    /**
     * @ORM\OneToMany(targetEntity="Av", mappedBy="etude", cascade={"persist", "remove"})
     */
    private $avs;

    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="GroupePhases", mappedBy="etude", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"numero" = "ASC"})
     */
    private $groupes;

    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="Phase", mappedBy="etude", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $phases;

    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="Mission", mappedBy="etude", cascade={"persist","remove"}, orphanRemoval=true)
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
     * @var string
     *
     * @ORM\Column(name="presentationProjet", type="text", nullable=true)
     */
    private $presentationProjet;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptionPrestation", type="text", nullable=true)
     */
    private $descriptionPrestation;

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
     * @var Prospect
     */
    private $newProspect;

    /**
     * @Assert\Valid()
     * @ORM\OneToOne(targetEntity=Bdc::class, inversedBy="etude", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $bdc;

    /**
     * @Assert\Valid()
     * @ORM\ManyToOne(targetEntity=Cca::class, inversedBy="etudes", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $cca;

    /**
     * @var bool est-ce que l'étude utilise la CCa/BdC ? Ce booléen sert à forcer
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ccaActive;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->dateCreation = $this->dateCreation ?? new \DateTime('now');
        $this->dateModification = $this->dateModification ?? new \DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->dateModification = new \DateTime('now');
    }

    /**
     * @ORM\PostPersist
     */
    public function createThread(LifecycleEventArgs $args)
    {
        if (null === $this->getThread()) {
            $em = $args->getObjectManager();
            $t = new Thread();
            $this->setThread($t);
            $this->getThread()->setId('etude_' . $this->getId());
            $this->getThread()->setPermalink('fake');
            $em->persist($t);
            $em->flush();
        }
    }

    /**
     * ADDITIONAL GETTERS/SETTERS.
     */

    /**
     * @param string $namingConvention
     *
     * @return string
     *
     * @internal Should not be used in controllers, hardly in doctypes
     * Because of different naming conventions between, reference should not be used anymore. References should be
     * manually handed in your doctypes.
     */
    public function getReference($namingConvention = 'id')
    {
        return 'nom' == $namingConvention ? $this->getNom() :
            ('numero' === $namingConvention ? $this->getNumero() : $this->getId());
    }

    public function getFa()
    {
        foreach ($this->factures as $facture) {
            if (Facture::TYPE_VENTE_ACCOMPTE == $facture->getType()) {
                return $facture;
            }
        }

        return null;
    }

    public function getFs()
    {
        foreach ($this->factures as $facture) {
            if (Facture::TYPE_VENTE_SOLDE == $facture->getType()) {
                return $facture;
            }
        }

        return null;
    }

    public function getNumero()
    {
        return $this->num;
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
     * @return \DateTime
     */
    public function getDateLancement()
    {
        if ($this->ce) {// Réel
            return $this->ce->getDateSignature();
        }
        if ($this->bdc) {// Réel
            return $this->bdc->getDateSignature();
        }
        if ($this->cc) { // Réel
            return $this->cc->getDateSignature();
        }

        // Théorique
        $dateDebut = [];
        $phases = $this->phases;
        foreach ($phases as $phase) {
            if (null !== $phase->getDateDebut()) {
                array_push($dateDebut, $phase->getDateDebut());
            }
        }

        if (count($dateDebut) > 0) {
            return min($dateDebut);
        }

        return null;
    }

    /**
     * Renvoie la date de fin : Fin de la phase la plus en aval.
     *
     * @param bool $avecAvenant
     *
     * @return \DateTime
     */
    public function getDateFin($avecAvenant = false)
    {
        $dateFin = [];
        $phases = $this->phases;

        /** @var Phase $p */
        foreach ($phases as $p) {
            if (null !== $p->getDateDebut() && null !== $p->getDelai()) {
                $dateDebut = clone $p->getDateDebut();
                array_push($dateFin, $dateDebut->modify('+' . $p->getDelai() . ' day'));
                unset($dateDebut);
            }
        }

        if (count($dateFin) > 0) {
            $dateFin = max($dateFin);
            if ($avecAvenant && $this->avs && $this->avs->last()) {
                $dateFin->modify('+' . $this->avs->last()->getDifferentielDelai() . ' day');
            }

            return $dateFin;
        }

        return null;
    }

    public function getDelai($avecAvenant = false)
    {
        if ($this->getDateFin($avecAvenant)) {
            if ($this->getDateLancement()) {
                return $this->getDateFin($avecAvenant)->diff($this->getDateLancement());
            }
        }

        return null;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->relatedDocuments = new ArrayCollection();
        $this->clientContacts = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->phases = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->missions = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->procesVerbaux = new ArrayCollection();
        $this->avs = new ArrayCollection();
        $this->avMissions = new ArrayCollection();
        $this->competences = new ArrayCollection();

        $this->fraisDossier = 90;
        $this->pourcentageAcompte = 0.40;
        $this->stateID = 1;
    }

    /**
     * @deprecated since 0 0
     *
     * @param     $doc
     * @param int $key
     *
     * @return mixed|null
     *
     * @throws \Exception
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
                throw new \Exception('Missing implementation of getFis() on Etude entity');
            case 'FS':
                return $this->getFs();
            case \App\Controller\Publish\TraitementController::DOCTYPE_PROCES_VERBAL_FINAL:
                return $this->getPvr();
            case \App\Controller\Publish\TraitementController::DOCTYPE_PROCES_VERBAL_INTERMEDIAIRE:
                return $this->getPvis($key);
            case 'AV':
                return $this->getAvs()->get($key);
            case 'RM':
                if (-1 == $key) {
                    return null;
                } else {
                    return $this->getMissions()->get($key);
                }
                // no break
            default:
                return null;
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
        return [
            '' => 'Non défini',
            '0' => 'Non auditée',
            '1' => 'Validée',
            '2' => 'Validée (documents manquants)',
            '3' => 'Problème mineur',
            '4' => 'Problème majeur',
        ];
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
     * Set prospect.
     *
     * @return Etude
     */
    public function setProspect(Prospect $prospect)
    {
        $this->prospect = $prospect;

        return $this;
    }

    /**
     * Get prospect.
     *
     * @return Prospect
     */
    public function getProspect()
    {
        return $this->prospect;
    }

    /**
     * Set suiveur.
     *
     * @param Personne $suiveur
     *
     * @return Etude
     */
    public function setSuiveur(Personne $suiveur = null)
    {
        $this->suiveur = $suiveur;

        return $this;
    }

    /**
     * Get suiveur.
     *
     * @return Personne
     */
    public function getSuiveur()
    {
        return $this->suiveur;
    }

    /**
     * @return mixed
     */
    public function getSuiveurQualite()
    {
        return $this->suiveurQualite;
    }

    /**
     * @param mixed $suiveurQualite
     */
    public function setSuiveurQualite($suiveurQualite)
    {
        $this->suiveurQualite = $suiveurQualite;
    }

    /**
     * Add clientContacts.
     *
     * @return Etude
     */
    public function addClientContact(ClientContact $clientContacts)
    {
        $this->clientContacts[] = $clientContacts;

        return $this;
    }

    /**
     * Remove clientContacts.
     */
    public function removeClientContact(ClientContact $clientContacts)
    {
        $this->clientContacts->removeElement($clientContacts);
    }

    /**
     * Get clientContacts.
     *
     * @return Collection
     */
    public function getClientContacts()
    {
        return $this->clientContacts;
    }

    /**
     * Add suivi.
     *
     * @return Etude
     */
    public function addSuivi(Suivi $suivi)
    {
        $this->suivis[] = $suivi;

        return $this;
    }

    /**
     * Remove suivi.
     */
    public function removeSuivi(Suivi $suivi)
    {
        $this->suivis->removeElement($suivi);
    }

    /**
     * Get suivis.
     *
     * @return Collection
     */
    public function getSuivis()
    {
        return $this->suivis;
    }

    /**
     * Set ap.
     *
     * @param Ap $ap
     *
     * @return Etude
     */
    public function setAp(?Ap $ap = null)
    {
        if (null !== $ap) {
            $ap->setEtude($this);
        }

        $this->ap = $ap;

        return $this;
    }

    /**
     * Get ap.
     *
     * @return Ap
     */
    public function getAp()
    {
        return $this->ap;
    }

    /**
     * Add phases.
     *
     * @return Etude
     */
    public function addPhase(Phase $phases)
    {
        $this->phases[] = $phases;

        return $this;
    }

    /**
     * Remove phases.
     */
    public function removePhase(Phase $phases)
    {
        $this->phases->removeElement($phases);
    }

    /**
     * Get phases.
     *
     * @return Collection
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * Set cc.
     *
     * @param Cc $cc
     *
     * @return Etude
     */
    public function setCc(?Cc $cc = null)
    {
        if (null !== $cc) {
            $cc->setEtude($this);
        }

        $this->cc = $cc;

        return $this;
    }

    /**
     * Get cc.
     *
     * @return Cc
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set ce.
     *
     * @param Ce $ce
     *
     * @return Etude
     */
    public function setCe(?Ce $ce = null)
    {
        if (null !== $ce) {
            $ce->setEtude($this);
        }

        $this->ce = $ce;

        return $this;
    }

    /**
     * Get ce.
     *
     * @return Ce
     */
    public function getCe()
    {
        return $this->ce;
    }

    /**
     * Add mission.
     *
     * @return Etude
     */
    public function addMission(DocType $mission)
    {
        $this->missions[] = $mission;

        return $this;
    }

    /**
     * Remove missions.
     */
    public function removeMission(DocType $mission)
    {
        $this->missions->removeElement($mission);
    }

    /**
     * Get missions.
     *
     * @return Collection
     */
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * Add Facture.
     *
     * @return Etude
     */
    public function addFacture(Facture $facture)
    {
        $this->factures[] = $facture;

        return $this;
    }

    /**
     * Remove Facture.
     */
    public function removeFacture(Facture $facture)
    {
        $this->factures->removeElement($facture);
    }

    /**
     * Get factures.
     *
     * @return Collection
     */
    public function getFactures()
    {
        return $this->factures;
    }

    /**
     * Remove procesVerbal.
     */
    public function removeProcesVerbal(ProcesVerbal $pv)
    {
        $this->procesVerbaux->removeElement($pv);
    }

    /**
     * Get factures.
     *
     * @return Collection
     */
    public function getProcesVerbaux()
    {
        return $this->procesVerbaux;
    }

    /**
     * Add pvis.
     *
     * @return Etude
     */
    public function addPvi(ProcesVerbal $pvi)
    {
        $this->procesVerbaux[] = $pvi;
        $pvi->setEtude($this);
        $pvi->setType('pvi');

        return $this;
    }

    /**
     * Remove pvis.
     */
    public function removePvi(ProcesVerbal $pvis)
    {
        $this->procesVerbaux->removeElement($pvis);
    }

    /**
     * Get pvis.
     *
     * @param int $key
     *
     * @return mixed
     */
    public function getPvis($key = -1)
    {
        $pvis = [];

        foreach ($this->procesVerbaux as $value) {
            if ('pvi' == $value->getType()) {
                $pvis[] = $value;
            }
        }

        if ($key >= 0) {
            if ($key < count($pvis)) {
                return $pvis[$key];
            } else {
                return null;
            }
        }

        usort($pvis, [$this, 'trieDateSignature']);

        return $pvis;
    }

    /**
     * Add avs.
     *
     * @return Etude
     */
    public function addAv(Av $avs)
    {
        $this->avs[] = $avs;

        return $this;
    }

    /**
     * Remove avs.
     */
    public function removeAv(Av $avs)
    {
        $this->avs->removeElement($avs);
    }

    /**
     * Get avs.
     *
     * @return Collection
     */
    public function getAvs()
    {
        return $this->avs;
    }

    /**
     * Add avMissions.
     *
     * @return Etude
     */
    public function addAvMission(AvMission $avMissions)
    {
        $this->avMissions[] = $avMissions;

        return $this;
    }

    /**
     * Remove avMissions.
     */
    public function removeAvMission(AvMission $avMissions)
    {
        $this->avMissions->removeElement($avMissions);
    }

    /**
     * Get avMissions.
     *
     * @return Collection
     */
    public function getAvMissions()
    {
        return $this->avMissions;
    }

    /**
     * Set pvr.
     *
     * @return Etude
     */
    public function setPvr(ProcesVerbal $pvr)
    {
        $pvr->setEtude($this);
        $pvr->setType('pvr');

        foreach ($this->procesVerbaux as $pv) {
            if ('pvr' === $pv->getType()) {
                return $this;
            }
        }
        $this->procesVerbaux[] = $pvr;

        return $this;
    }

    /**
     * Get pvr.
     *
     * @return ProcesVerbal
     */
    public function getPvr()
    {
        foreach ($this->procesVerbaux as $pv) {
            if ('pvr' == $pv->getType()) {
                return $pv;
            }
        }

        return null;
    }

    /**
     * Set thread.
     *
     * @return Etude
     */
    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread.
     *
     * @return Thread
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

    /**
     * @deprecated Since 2.2.0. use Etude_State_Array directly instead
     */
    public static function getStateIDChoice()
    {
        return self::ETUDE_STATE_ARRAY;
    }

    public static function getStateIDChoiceAssert()
    {
        return array_keys(self::ETUDE_STATE_ARRAY);
    }

    public function getStateIDToString()
    {
        return $this->stateID ? self::ETUDE_STATE_ARRAY[$this->stateID] : '';
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
     * Set auditCommentaires.
     *
     * @param string $auditCommentaires
     *
     * @return Etude
     */
    public function setAuditCommentaires($auditCommentaires)
    {
        $this->auditCommentaires = $auditCommentaires;

        return $this;
    }

    /**
     * Get auditCommentaires.
     *
     * @return string
     */
    public function getAuditCommentaires()
    {
        return $this->auditCommentaires;
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
     * Set if CE is active.
     *
     * @return Etude
     */
    public function setCeActive(?bool $ceActive)
    {
        $this->ceActive = $ceActive;

        return $this;
    }

    /**
     * Get if CE is active.
     *
     * @return bool
     */
    public function getCeActive()
    {
        return $this->ceActive;
    }

    /**
     * Add groupes.
     *
     * @return Etude
     */
    public function addGroupe(GroupePhases $groupe)
    {
        $this->groupes[] = $groupe;

        return $this;
    }

    /**
     * Remove groupes.
     */
    public function removeGroupe(GroupePhases $groupe)
    {
        $this->groupes->removeElement($groupe);
    }

    /**
     * Get groupes.
     *
     * @return Collection
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
        return [
            1 => 'Kiwi',
            2 => 'Etude avec l\'Ecole',
            3 => 'Relation école (EPRD, Incubateur, Direction...)',
            4 => 'Participation aux évènements',
            5 => 'Réseaux des Anciens',
            6 => 'Réseaux des élèves',
            7 => 'Contact spontané',
            8 => 'Ancien client',
            9 => 'Dev\'Co',
            10 => 'Partenariat JE',
            11 => 'Autre',
        ];
    }

    public function getSourceDeProspectionToString()
    {
        $tab = $this->getSourceDeProspectionChoice();

        return $this->sourceDeProspection ? $tab[$this->sourceDeProspection] : '';
    }

    /**
     * Add procesVerbaux.
     *
     * @return Etude
     */
    public function addProcesVerbaux(ProcesVerbal $procesVerbaux)
    {
        $this->procesVerbaux[] = $procesVerbaux;

        return $this;
    }

    /**
     * Remove procesVerbaux.
     */
    public function removeProcesVerbaux(ProcesVerbal $procesVerbaux)
    {
        $this->procesVerbaux->removeElement($procesVerbaux);
    }

    private function trieDateSignature(DocType $a, DocType $b)
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
     * @return Etude
     */
    public function addRelatedDocument(RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments[] = $relatedDocuments;

        return $this;
    }

    /**
     * Remove relatedDocuments.
     */
    public function removeRelatedDocument(RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments->removeElement($relatedDocuments);
    }

    /**
     * Get relatedDocuments.
     *
     * @return Collection
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
     * @param DomaineCompetence $domaineCompetence
     *
     * @return Etude
     */
    public function setDomaineCompetence(DomaineCompetence $domaineCompetence = null)
    {
        $this->domaineCompetence = $domaineCompetence;

        return $this;
    }

    /**
     * Get domaineCompetence.
     *
     * @return DomaineCompetence
     */
    public function getDomaineCompetence()
    {
        return $this->domaineCompetence;
    }

    /**
     * Add competences.
     */
    public function addCompetence(Competence $competence)
    {
        $this->competences[] = $competence;
        $competence->addEtude($this);
    }

    /**
     * Remove competences.
     */
    public function removeCompetence(Competence $competence)
    {
        $this->competences->removeElement($competence);
        $competence->removeEtude($this);
    }

    /**
     * Get competences.
     *
     * @return ArrayCollection
     */
    public function getCompetences()
    {
        return $this->competences;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    public function getCdc(): ?string
    {
        return $this->cdc;
    }

    public function setCdc(?string $cdc): self
    {
        $this->cdc = $cdc;

        return $this;
    }

    public function getPc(): ?string
    {
        return $this->pc;
    }

    public function setPc(?string $pc): self
    {
        $this->pc = $pc;

        return $this;
    }

    public function getQs(): ?string
    {
        return $this->qs;
    }

    public function setQs(?string $qs): self
    {
        $this->qs = $qs;

        return $this;
    }

    public function getBdc(): ?Bdc
    {
        return $this->bdc;
    }

    public function setBdc(?Bdc $bdc = null): self
    {
        if (null !== $bdc) {
            $bdc->setEtude($this);
        }

        $this->bdc = $bdc;

        return $this;
    }

    public function getCca(): ?Cca
    {
        return $this->cca;
    }

    public function setCca(?Cca $cca = null): self
    {
        $this->cca = $cca;

        return $this;
    }

    public function getCcaActive(): ?bool
    {
        return $this->ccaActive;
    }

    public function setCcaActive(?bool $ccaActive): self
    {
        $this->ccaActive = $ccaActive;

        return $this;
    }
}
