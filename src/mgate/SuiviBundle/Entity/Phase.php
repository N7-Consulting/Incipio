<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\SuiviBundle\Entity\Phase
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\PhaseRepository")
 */
class Phase
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=true)
     * todo enlever le nullable=true
     */
    private $position;
    
    /**
     * Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="phases", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;
    
    //TO DELETE
    /**
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\PhaseMission", mappedBy="phase", cascade={"persist"})
     */
    private $phaseMission;

    /**
     * @var integer $nbrJEH
     *
     * @ORM\Column(name="nbrJEH", type="integer", nullable=true)
     */
    private $nbrJEH;
    
    /**
     * @var integer $prixJEH
     *
     * @ORM\Column(name="prixJEH", type="integer", nullable=true)
     * @Assert\Min(80)
     * @Assert\Max(300)
     */
    private $prixJEH;
    
   /**
     * @var string $titre
     *
     * @ORM\Column(name="titre", type="text", nullable=true)
     */
    private $titre;

    /**
     * @var string $objectif
     *
     * @ORM\Column(name="objectif", type="text", nullable=true)
     */
    private $objectif;
    
    /**
     * @var string $methodo
     *
     * @ORM\Column(name="methodo", type="text", nullable=true)
     */
    private $methodo;
    
    /**
     * @var \DateTime $dateDebut
     *
     * @ORM\Column(name="dateDebut", type="datetime", nullable=true)
     */
    private $dateDebut;
    
    /**
     * @var string $delai
     *
     * @ORM\Column(name="delai", type="text", nullable=true)
     */
    private $delai;
    
    /**
     * @var integer $validation
     *
     * @ORM\Column(name="validation", type="integer", nullable=false)
     * @Assert\Choice(callback = "getValidationChoiceAssert")
     */
    private $validation;
    
    /**
     * @var interger $avenant
     * @abstract statu de la phase en cas d'avenant : 0 : Phase original | 1 : Phase ajoutée | -1 : Phase supprimée
     * @ORM\Column(name="avanantStatut", type="integer", options={"default"=0})
     */
    private $avenantStatut;
    
    /**
     * @ORM\OneToOne(targetEntity="Phase")
     */
    private $avenantModification;
        
    
    public function __construct()
    {
        $this->voteCount = 0;
        $this->createdAt = new \DateTime('now');
        //$this->isEnabled = false;
        $this->prixJEH = 300;
        $this->validation = 1;
        $this->avenantStatut = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set etude
     *
     * @param mgate\SuiviBundle\Entity\Etude $etude
     * @return Phase
     */
    public function setEtude(\mgate\SuiviBundle\Entity\Etude $etude)
    {
        $this->etude = $etude;
    
        return $this;
    }

    /**
     * Get etude
     *
     * @return mgate\SuiviBundle\Entity\Etude 
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set nbrJEH
     *
     * @param integer $nbrJEH
     * @return Phase
     */
    public function setNbrJEH($nbrJEH)
    {
        $this->nbrJEH = $nbrJEH;
    
        return $this;
    }

    /**
     * Get nbrJEH
     *
     * @return integer 
     */
    public function getNbrJEH()
    {
        return $this->nbrJEH;
    }

    /**
     * Set prixJEH
     *
     * @param integer $prixJEH
     * @return Phase
     */
    public function setPrixJEH($prixJEH)
    {
        $this->prixJEH = $prixJEH;
    
        return $this;
    }

    /**
     * Get prixJEH
     *
     * @return integer 
     */
    public function getPrixJEH()
    {
        return $this->prixJEH;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Phase
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set objectif
     *
     * @param string $objectif
     * @return Phase
     */
    public function setObjectif($objectif)
    {
        $this->objectif = $objectif;
    
        return $this;
    }

    /**
     * Get objectif
     *
     * @return string 
     */
    public function getObjectif()
    {
        return $this->objectif;
    }

    /**
     * Set methodo
     *
     * @param string $methodo
     * @return Phase
     */
    public function setMethodo($methodo)
    {
        $this->methodo = $methodo;
    
        return $this;
    }

    /**
     * Get methodo
     *
     * @return string 
     */
    public function getMethodo()
    {
        return $this->methodo;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Phase
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
    
        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set delai
     *
     * @param string $delai
     * @return Phase
     */
    public function setDelai($delai)
    {
        $this->delai = $delai;
    
        return $this;
    }

    /**
     * Get delai
     *
     * @return string 
     */
    public function getDelai()
    {
        return $this->delai;
    }
    
    /**
     * Set position
     *
     * @param string $position
     * @return integer
     */
    public function setPosition($position)
    {
        $this->position = $position;
        
        return $this;
    }
    
    /**
     * Get position
     *
     * @return integer 
     */ 
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set validation
     *
     * @param integer $validation
     * @return Phase
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;
    
        return $this;
    }

    /**
     * Get validation
     *
     * @return integer 
     */
    public function getValidation()
    {
        return $this->validation;
    }
    
    public static function getValidationChoice()
    {
        return array(   //0 => "Aucune", //Inutile
                        1 => "Cette phase sera soumise à une validation orale lors d’un entretien avec le client.",
                        2 => "Cette phase sera soumise à une validation écrite qui prend la forme d’un Procès-Verbal Intermédiaire signé par le client.");
    }
    public static function getValidationChoiceAssert()
    {
        return array_keys(Phase::getValidationChoice());
    }
    
    public function getValidationToString()
    {
        $tab = $this->getValidationChoice();
        return $tab[$this->validation];
    }
    
    /**
     * Add phaseMission
     *
     * @param \mgate\SuiviBundle\Entity\PhaseMission $phaseMission
     * @return Phase
     */
    public function addPhaseMission(\mgate\SuiviBundle\Entity\PhaseMission $phaseMission)
    {
        $this->phaseMission[] = $phaseMission;
    
        return $this;
    }

    /**
     * Remove phaseMission
     *
     * @param \mgate\SuiviBundle\Entity\PhaseMission $phaseMission
     */
    public function removePhaseMission(\mgate\SuiviBundle\Entity\PhaseMission $phaseMission)
    {
        $this->phaseMission->removeElement($phaseMission);
    }

    /**
     * Get phaseMission
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhaseMission()
    {
        return $this->phaseMission;
    }

    /**
     * Set avenantStatut
     *
     * @param integer $avenantStatut
     * @return Phase
     */
    public function setAvenantStatut($avenantStatut)
    {
        $this->avenantStatut = $avenantStatut;
    
        return $this;
    }

    /**
     * Get avenantStatut
     *
     * @return integer 
     */
    public function getAvenantStatut()
    {
        return $this->avenantStatut;
    }

    /**
     * Set avenantModification
     *
     * @param \mgate\SuiviBundle\Entity\Phase $avenantModification
     * @return Phase
     */
    public function setAvenantModification(\mgate\SuiviBundle\Entity\Phase $avenantModification = null)
    {
        $this->avenantModification = $avenantModification;
    
        return $this;
    }

    /**
     * Get avenantModification
     *
     * @return \mgate\SuiviBundle\Entity\Phase 
     */
    public function getAvenantModification()
    {
        return $this->avenantModification;
    }
}