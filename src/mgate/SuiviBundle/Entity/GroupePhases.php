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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use mgate\SuiviBundle\Entity\Phase;

/**
 * mgate\SuiviBundle\Entity\GroupePhases.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\GroupePhasesRepository")
 */
class GroupePhases
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
     * Gedmo\SortableGroup.
     *
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="groupes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="smallint")
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Phase", mappedBy="groupe")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $phases;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->phases = new ArrayCollection();
    }

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
     * @param mgate\SuiviBundle\Entity\Etude $etude
     *
     * @return GroupePhases
     */
    public function setEtude($etude = null)
    {
        $this->etude = $etude;

        return $this;
    }

    /**
     * Get etude.
     *
     * @return mgate\SuiviBundle\Entity\Etude
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set titre.
     *
     * @param string $titre
     *
     * @return GroupePhases
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     *
     * @return titre
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set numero.
     *
     * @param int $numero
     *
     * @return GroupePhases
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return GroupePhases
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
     * Add phases.
     *
     * @param Phase $phases
     *
     * @return GroupePhases
     */
    public function addPhase(Phase $phases)
    {
        $this->phases[] = $phases;

        return $this;
    }

    /**
     * Remove phases.
     *
     * @param Phase $phases
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
        return 'Groupe : '.$this->getTitre();
    }
}
