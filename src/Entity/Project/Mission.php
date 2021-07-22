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

use App\Entity\Personne\Membre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Project\MissionRepository")
 */
class Mission extends DocType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Etude
     *
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="missions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Membre")
     * @ORM\JoinColumn(nullable=true)
     */
    private $referentTechnique;

    /**
     * @var Membre
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Membre", inversedBy="missions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $intervenant;

    /**
     * @var \DateTime
     *
     * @Assert\NotNull()
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="debutOm", type="datetime", nullable=true)
     */
    private $debutOm;

    /**
     * @var \DateTime
     *
     * @Assert\NotNull()
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="finOm", type="datetime", nullable=true)
     */
    private $finOm;

    /**
     * @var float
     *
     * @Assert\NotNull()
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="pourcentageJunior", type="float", nullable=false)
     * Réel compris entre 0 et 1 représentant le pourcentage de la junior sur cette mission.
     */
    private $pourcentageJunior;

    /**
     * @var RepartitionJEH
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Project\RepartitionJEH", mappedBy="mission",  cascade="all",
     *                                                                        orphanRemoval=true, fetch="EAGER")
     */
    private $repartitionsJEH;

    /**
     * @deprecated Use phase in RepartitionJEH instead. Here for backward compatibility.
     * @var Phase
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Project\Phase", mappedBy="mission", cascade={"merge"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $phases;

    /**
     * @var int
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="avancement", type="integer", nullable=true)
     */
    private $avancement;

    /**
     * @var bool
     *
     * @ORM\Column(name="rapportDemande", type="boolean", nullable=true)
     */
    private $rapportDemande;

    /**
     * @var bool
     *
     * @ORM\Column(name="rapportRelu", type="boolean", nullable=true)
     */
    private $rapportRelu;

    /**
     * @var bool
     *
     * @ORM\Column(name="remunere", type="boolean", nullable=true)
     */
    private $remunere;

    public function getReference()
    {
        return $this->getEtude()->getReference('nom') . '/' . (!empty($this->getDebutOm()) ? $this->getDebutOm()
                ->format('Y') : 'XX') .
            '/RM/' . $this->getVersion();
    }

    public function __construct()
    {
        parent::__construct();
        $this->repartitionsJEH = new ArrayCollection();
        $this->pourcentageJunior = 0.4;
    }

    public function __toString()
    {
        return 'RM - ' . $this->getIntervenant();
    }

    /**
     * @deprecated since version 0.1
     *
     * @return array('jehRemuneration','montantRemuneration');
     */
    public function getRemuneration()
    {
        $nbrJEHRemuneration = (int) 0;
        $prixRemuneration = (float) 0;
        foreach ($this->getRepartitionsJEH() as $repartitionJEH) {
            $nbrJEHRemuneration += $repartitionJEH->getNbrJEH();
            $prixRemuneration += $repartitionJEH->getNbrJEH() * $repartitionJEH->getPrixJEH();
        }
        $prixRemuneration *= 1 - $this->getPourcentageJunior();

        return ['jehRemuneration' => $nbrJEHRemuneration, 'montantRemuneration' => $prixRemuneration];
    }

    /**
     * @Groups({"gdpr"})
     *
     * @return float|int
     */
    public function getRemunerationBrute()
    {
        $prixRemuneration = (float) 0;
        foreach ($this->getRepartitionsJEH() as $repartitionJEH) {
            $prixRemuneration += $repartitionJEH->getNbrJEH() * $repartitionJEH->getPrixJEH();
        }
        $prixRemuneration *= 1 - $this->getPourcentageJunior();

        return $prixRemuneration;
    }

    /**
     * @Groups({"gdpr"})
     *
     * @return int
     */
    public function getNbrJEH()
    {
        $nbr = 0;
        foreach ($this->repartitionsJEH as $repartition) {
            $nbr += $repartition->getNbrJEH();
        }

        return $nbr;
    }

    // Block astuce pour ajout direct d'intervenant dans formulaire
    public function getMission()
    {
        return $this;
    }

    private $knownIntervenant = false;

    private $newIntervenant;

    public function isKnownIntervenant()
    {
        return $this->knownIntervenant;
    }

    public function setKnownIntervenant($boolean)
    {
        $this->knownIntervenant = $boolean;
    }

    public function getNewIntervenant()
    {
        return $this->newIntervenant;
    }

    public function setNewIntervenant($var)
    {
        $this->newIntervenant = $var;
    }

    // Fin du block

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
     * Set intervenant.
     *
     * @return Mission
     */
    public function setIntervenant(Membre $intervenant)
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    /**
     * Get intervenant.
     *
     * @return Membre
     */
    public function getIntervenant()
    {
        return $this->intervenant;
    }

    /**
     * Set debutOm.
     *
     * @param \DateTime $debutOm
     *
     * @return Mission
     */
    public function setDebutOm($debutOm)
    {
        $this->debutOm = $debutOm;

        return $this;
    }

    /**
     * Get debutOm.
     *
     * @return \DateTime
     */
    public function getDebutOm()
    {
        return $this->debutOm;
    }

    /**
     * Set finOm.
     *
     * @param \DateTime $finOm
     *
     * @return Mission
     */
    public function setFinOm($finOm)
    {
        $this->finOm = $finOm;

        return $this;
    }

    /**
     * Get finOm.
     *
     * @return \DateTime
     */
    public function getFinOm()
    {
        return $this->finOm;
    }

    /**
     * Set avancement.
     *
     * @param int $avancement
     *
     * @return Mission
     */
    public function setAvancement($avancement)
    {
        $this->avancement = $avancement;

        return $this;
    }

    /**
     * Get avancement.
     *
     * @return int
     */
    public function getAvancement()
    {
        return $this->avancement;
    }

    /**
     * Set rapportDemande.
     *
     * @param bool $rapportDemande
     *
     * @return Mission
     */
    public function setRapportDemande($rapportDemande)
    {
        $this->rapportDemande = $rapportDemande;

        return $this;
    }

    /**
     * Get rapportDemande.
     *
     * @return bool
     */
    public function getRapportDemande()
    {
        return $this->rapportDemande;
    }

    /**
     * Set rapportRelu.
     *
     * @param bool $rapportRelu
     *
     * @return Mission
     */
    public function setRapportRelu($rapportRelu)
    {
        $this->rapportRelu = $rapportRelu;

        return $this;
    }

    /**
     * Get rapportRelu.
     *
     * @return bool
     */
    public function getRapportRelu()
    {
        return $this->rapportRelu;
    }

    /**
     * Set remunere.
     *
     * @param bool $remunere
     *
     * @return Mission
     */
    public function setRemunere($remunere)
    {
        $this->remunere = $remunere;

        return $this;
    }

    /**
     * Get remunere.
     *
     * @return bool
     */
    public function getRemunere()
    {
        return $this->remunere;
    }

    /**
     * Set etude.
     *
     * @return Mission
     */
    public function setEtude(Etude $etude)
    {
        $this->etude = $etude;

        return $this;
    }

    /**
     * Get etude.
     *
     * @return Etude
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set pourcentageJunior.
     *
     * @param int $pourcentageJunior
     *
     * @return Mission
     */
    public function setPourcentageJunior($pourcentageJunior)
    {
        $this->pourcentageJunior = $pourcentageJunior;

        return $this;
    }

    /**
     * Get pourcentageJunior.
     *
     * @return int
     */
    public function getPourcentageJunior()
    {
        return $this->pourcentageJunior;
    }

    /**
     * Set referentTechnique.
     *
     * @param Membre $referentTechnique
     *
     * @return Mission
     */
    public function setReferentTechnique(Membre $referentTechnique = null)
    {
        $this->referentTechnique = $referentTechnique;

        return $this;
    }

    /**
     * Get referentTechnique.
     *
     * @return Membre
     */
    public function getReferentTechnique()
    {
        return $this->referentTechnique;
    }

    /**
     * Add repartitionsJEH.
     *
     * @return Mission
     */
    public function addRepartitionsJEH(RepartitionJEH $repartitionsJEH)
    {
        $this->repartitionsJEH[] = $repartitionsJEH;

        return $this;
    }

    /**
     * Remove repartitionsJEH.
     */
    public function removeRepartitionsJEH(RepartitionJEH $repartitionsJEH)
    {
        $this->repartitionsJEH->removeElement($repartitionsJEH);
    }

    /**
     * Get repartitionsJEH.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRepartitionsJEH()
    {
        return $this->repartitionsJEH;
    }

    /**
     * @deprecated Use phase in RepartitionJEH instead. Here for backward compatibility.
     * Add phase.
     *
     * @return Mission
     */
    public function addPhase(Phase $phase)
    {
        $this->phases[] = $phase;
        $phase->setMission($this);

        return $this;
    }

    /**
     * @deprecated Use phase in RepartitionJEH instead. Here for backward compatibility.
     * Remove phase.
     */
    public function removePhase(Phase $phase)
    {
        $this->phases->removeElement($phase);
        $phase->setMission(null);
    }

    /**
     * @deprecated Use phase in RepartitionJEH instead. Here for backward compatibility.
     * Get phases.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhases()
    {
        return $this->phases;
    }
}
