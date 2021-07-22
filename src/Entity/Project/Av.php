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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Av extends DocType
{
    const CLAUSES_CHOICES = ['Avenant de Délai' => 1,
        'Avenant de Méthodologie' => 2,
        'Avenant de Montant' => 3,
        'Avenant de Rupture' => 5 ];

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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="avs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="differentielDelai", type="integer", nullable=false,  options={"default"=0})
     */
    private $differentielDelai;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="objet", type="text", nullable=false)
     */
    private $objet;

    /**
     * @var AvMission
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Project\AvMission", mappedBy="avenant", cascade={"persist","remove"})
     */
    private $avenantsMissions;

    /**
     * @var array
     *
     * @ORM\Column(name="clauses", type="array")
     */
    private $clauses;

    /**
     * @var ArrayCollection phase differentiel
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Project\Phase", mappedBy="avenant", cascade={"persist", "remove"})
     */
    private $phases;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numero;

    public function __construct()
    {
        parent::__construct();
        $this->avenantsMissions = new ArrayCollection();
        $this->phases = new ArrayCollection();
    }

    public static function getClausesKeys()
    {
        return array_flip(self::CLAUSES_CHOICES);
    }

    public function getReference()
    {
        return $this->numero . '-' . $this->etude->getReference('nom');
    }

    /** auto-generated methods */

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
     * Set etude.
     *
     * @return Av
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
     * Set differentielDelai.
     *
     * @param int $differentielDelai
     *
     * @return Av
     */
    public function setDifferentielDelai($differentielDelai)
    {
        $this->differentielDelai = $differentielDelai;

        return $this;
    }

    /**
     * Get differentielDelai.
     *
     * @return int
     */
    public function getDifferentielDelai()
    {
        return $this->differentielDelai;
    }

    /**
     * Set objet.
     *
     * @param string $objet
     *
     * @return Av
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet.
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Add avenantsMissions.
     *
     * @return Av
     */
    public function addAvenantsMission(AvMission $avenantsMissions)
    {
        $this->avenantsMissions[] = $avenantsMissions;

        return $this;
    }

    /**
     * Remove avenantsMissions.
     */
    public function removeAvenantsMission(AvMission $avenantsMissions)
    {
        $this->avenantsMissions->removeElement($avenantsMissions);
    }

    /**
     * Get avenantsMissions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvenantsMissions()
    {
        return $this->avenantsMissions;
    }

    /**
     * Set clauses.
     *
     * @param array $clauses
     *
     * @return Av
     */
    public function setClauses($clauses)
    {
        $this->clauses = $clauses;

        return $this;
    }

    /**
     * Get clauses.
     *
     * @return array
     */
    public function getClauses()
    {
        return $this->clauses;
    }

    /**
     * Add phases.
     *
     * @return Av
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhases()
    {
        return $this->phases;
    }

    public function __toString()
    {
        return $this->etude->getReference() . '/AV/' . $this->getId();
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero(int $numero)
    {
        $this->numero = $numero;

        return $this;
    }
}
