<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Formation;

use App\Entity\Personne\Personne;
use App\Entity\Publish\RelatedDocument;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formation.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Formation\FormationRepository")
 */
class Formation
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
     * @var int
     *
     * @ORM\Column(name="mandat", type="integer")
     * @Assert\NotBlank
     */
    private $mandat;

    /**
     * @var int
     *
     * @ORM\Column(name="categorie", type="integer")
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * @Assert\NotBlank
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @var Personne
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Personne\Personne")
     * @ORM\JoinTable(name="formation_formateurs")
     */
    private $formateurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Publish\RelatedDocument", mappedBy="formation", cascade={"remove"})
     */
    private $relatedDocuments;

    /**
     * @var Personne
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Personne\Personne")
     * @ORM\JoinTable(name="formation_membresPresents")
     */
    private $membresPresents;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateDebut", type="datetime")
     * @Assert\NotBlank
     */
    private $dateDebut;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateFin", type="datetime")
     * @Assert\NotBlank
     */
    private $dateFin;

    /**
     *  
     * @ORM\Column(name="doc", type="string", length=255, nullable=true)
     */
    private $docPath;

    /**
     * Get getCategoriesChoice.
     *
     * @return array
     */
    public static function getCategoriesChoice()
    {
        return [
            1 => 'Gestion Associative',
            2 => 'R.F.P',
            3 => 'Gestion d\'étude',
            4 => 'Trésorerie',
            5 => 'Développement Commercial',
            6 => 'Qualité',
            7 => 'Autre', ];
    }

    public function getCategoriesChoiceToString()
    {
        $tab = $this->getCategoriesChoice();      

        return $this->categorie ? $tab[$this->categorie] : '';
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
     * Set categorie.
     *
     * @param int $categorie
     *
     * @return Formation
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie.
     *
     * @return int
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set titre.
     *
     * @param string $titre
     *
     * @return Formation
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
     * Set description.
     *
     * @param string $description
     *
     * @return Formation
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
     * Set formateurs.
     *
     * @param \stdClass $formateurs
     *
     * @return Formation
     */
    public function setFormateurs($formateurs)
    {
        $this->formateurs = $formateurs;

        return $this;
    }

    /**
     * Get formateurs.
     *
     * @return Personne[]|ArrayCollection
     */
    public function getFormateurs()
    {
        return $this->formateurs;
    }

    /**
     * Set membresPresents.
     *
     * @param \stdClass $membresPresents
     *
     * @return Formation
     */
    public function setMembresPresents($membresPresents)
    {
        $this->membresPresents = $membresPresents;

        return $this;
    }

    /**
     * Get membresPresents.
     *
     * @return Personne[]|ArrayCollection
     */
    public function getMembresPresents()
    {
        return $this->membresPresents;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->formateurs = new ArrayCollection();
        $this->membresPresents = new ArrayCollection();
    }

    /**
     * Set dateDebut.
     *
     * @param \DateTime $dateDebut
     *
     * @return Formation
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
     * Set dateFin.
     *
     * @param \DateTime $dateFin
     *
     * @return Formation
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin.
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set docPath.
     *
     * @param string $docPath
     *
     * @return Formation
     */
    public function setDocPath($docPath)
    {
        $this->docPath = $docPath;

        return $this;
    }

    /**
     * Get docPath.
     *
     * @return string
     */
    public function getDocPath()
    {
        return $this->docPath;
    }

    /**
     * Add formateurs.
     *
     * @return Formation
     */
    public function addFormateur(Personne $formateurs)
    {
        $this->formateurs[] = $formateurs;

        return $this;
    }

    /**
     * Remove formateurs.
     */
    public function removeFormateur(Personne $formateurs)
    {
        $this->formateurs->removeElement($formateurs);
    }

    /**
     * Add membresPresents.
     *
     * @return Formation
     */
    public function addMembresPresent(Personne $membresPresents)
    {
        $this->membresPresents[] = $membresPresents;

        return $this;
    }

    /**
     * Remove membresPresents.
     */
    public function removeMembresPresent(Personne $membresPresents)
    {
        $this->membresPresents->removeElement($membresPresents);
    }

    /**
     * Set mandat.
     *
     * @param int $mandat
     *
     * @return Formation
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
     * Add relatedDocuments.
     *
     * @return Processus
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

}
