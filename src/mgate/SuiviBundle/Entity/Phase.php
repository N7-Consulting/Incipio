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
 * mgate\SuiviBundle\Entity\Phase.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\PhaseRepository")
 */
class Phase
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="phases", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;

    /**
     * @ORM\ManyToOne(targetEntity="GroupePhases", inversedBy="phases")
     * @ORM\OrderBy({"numero" = "ASC"})
     */
    private $groupe;

    /**
     * Gedmo\SortablePosition.
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     * todo enlever le nullable=true
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrJEH", type="integer", nullable=true)
     */
    private $nbrJEH;

    /**
     * @var int
     *
     * @ORM\Column(name="prixJEH", type="integer", nullable=true)
     */
    private $prixJEH;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="text", nullable=true)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="objectif", type="text", nullable=true)
     */
    private $objectif;

    /**
     * @var string
     *
     * @ORM\Column(name="methodo", type="text", nullable=true)
     */
    private $methodo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @var int
     *
     * @ORM\Column(name="delai", type="integer", nullable=true)
     */
    private $delai;

    /**
     * @var int
     *
     * @ORM\Column(name="validation", type="integer", nullable=true)
     * @Assert\Choice(callback = "getValidationChoiceAssert")
     */
    private $validation;

    /**
     * @ORM\ManyToOne(targetEntity="Av", inversedBy="phases")
     */
    private $avenant;

    /**
     * @var int 0 : modifiée, 1:ajoutée -1 : supprimée
     *
     * @ORM\Column(name="etatSurAvenant", type="integer", nullable=true)
     */
    private $etatSurAvenant;

    /**
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Mission", inversedBy="phases", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $mission;

    /**
     * ADDITIONAL GETTERS/SETTERS.
     */
    public function getMontantHT()
    {
        return $this->nbrJEH * $this->prixJEH;
    }

    public function getDateFin()
    {
        if ($this->dateDebut) {
            $date = clone $this->dateDebut;
            $date->modify('+ '.$this->delai.' day');

            return $date;
        } else {
            return new \DateTime('now');
        }
    }

    public function __construct()
    {
        $this->voteCount = 0;
        $this->createdAt = new \DateTime('now');
        $this->prixJEH = 320;
        $this->validation = 0;
        $this->avenantStatut = 0;
    }

    public function __toString()
    {
        return 'Phase : '.$this->getTitre();
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
     * @return Phase
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
     * Set etude.
     *
     * @param mgate\SuiviBundle\Entity\GroupePhases $groupe
     *
     * @return Phase
     */
    public function setGroupe($groupe = null)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe.
     *
     * @return mgate\SuiviBundle\Entity\GroupePhases
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set nbrJEH.
     *
     * @param int $nbrJEH
     *
     * @return Phase
     */
    public function setNbrJEH($nbrJEH)
    {
        $this->nbrJEH = $nbrJEH;

        return $this;
    }

    /**
     * Get nbrJEH.
     *
     * @return int
     */
    public function getNbrJEH()
    {
        return $this->nbrJEH;
    }

    /**
     * Set prixJEH.
     *
     * @param int $prixJEH
     *
     * @return Phase
     */
    public function setPrixJEH($prixJEH)
    {
        $this->prixJEH = $prixJEH;

        return $this;
    }

    /**
     * Get prixJEH.
     *
     * @return int
     */
    public function getPrixJEH()
    {
        return $this->prixJEH;
    }

    /**
     * Set titre.
     *
     * @param string $titre
     *
     * @return Phase
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set objectif.
     *
     * @param string $objectif
     *
     * @return Phase
     */
    public function setObjectif($objectif)
    {
        $this->objectif = $objectif;

        return $this;
    }

    /**
     * Get objectif.
     *
     * @return string
     */
    public function getObjectif()
    {
        return $this->objectif;
    }

    /**
     * Set methodo.
     *
     * @param string $methodo
     *
     * @return Phase
     */
    public function setMethodo($methodo)
    {
        $this->methodo = $methodo;

        return $this;
    }

    /**
     * Get methodo.
     *
     * @return string
     */
    public function getMethodo()
    {
        return $this->methodo;
    }

    /**
     * Set dateDebut.
     *
     * @param \DateTime $dateDebut
     *
     * @return Phase
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut.
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set delai.
     *
     * @param string $delai
     *
     * @return Phase
     */
    public function setDelai($delai)
    {
        $this->delai = $delai;

        return $this;
    }

    /**
     * Get delai.
     *
     * @return string
     */
    public function getDelai()
    {
        return $this->delai;
    }

    /**
     * Set position.
     *
     * @param string $position
     *
     * @return int
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set validation.
     *
     * @param int $validation
     *
     * @return Phase
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * Get validation.
     *
     * @deprecated since version 0.0
     *
     * @return int
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * @deprecated since version 0.0
     */
    public static function getValidationChoice()
    {
        return array(//0 => "Aucune", //Inutile
                        1 => 'Cette phase sera soumise à une validation orale lors d’un entretien avec le client.',
                        2 => 'Cette phase sera soumise à une validation écrite qui prend la forme d’un Procès-Verbal Intermédiaire signé par le client.', );
    }

    /**
     * @deprecated since version 0.0
     */
    public static function getValidationChoiceAssert()
    {
        return array_keys(self::getValidationChoice());
    }

    public function getValidationToString()
    {
        $tab = $this->getValidationChoice();

        return $tab[$this->validation];
    }

    /**
     * Set avenant.
     *
     * @param \mgate\SuiviBundle\Entity\Av $avenant
     *
     * @return Phase
     */
    public function setAvenant(\mgate\SuiviBundle\Entity\Av $avenant = null)
    {
        $this->avenant = $avenant;

        return $this;
    }

    /**
     * Get avenant.
     *
     * @return \mgate\SuiviBundle\Entity\Av
     */
    public function getAvenant()
    {
        return $this->avenant;
    }

    public static function getEtatSurAvenantChoice()
    {
        return array(0 => 'Modifiée', //Inutile
                        1 => 'Ajoutée',
                        -1 => 'Supprimée', );
    }
    public static function getEtatSurAvenantChoiceAssert()
    {
        return array_keys(self::getEtatSurAvenantChoice());
    }

    public function getEtatSurAvenantToString()
    {
        $tab = $this->getValidationChoice();

        return $tab[$this->validation];
    }

    /**
     * Set etatSurAvenant.
     *
     * @param int $etatSurAvenant
     *
     * @return Pha
     */
    public function setEtatSurAvenant($etatSurAvenant)
    {
        $this->etatSurAvenant = $etatSurAvenant;

        return $this;
    }

    /**
     * Get etatSurAvenant.
     *
     * @return int
     */
    public function getEtatSurAvenant()
    {
        return $this->etatSurAvenant;
    }

    /**
     * @return mixed
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * @param mixed $mission
     */
    public function setMission($mission)
    {
        $this->mission = $mission;
    }
}
