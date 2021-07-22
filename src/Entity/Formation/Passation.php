<?php

namespace App\Entity\Formation;

use App\Repository\Formation\PassationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Publish\RelatedDocument;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PassationRepository::class)
 */
class Passation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Publish\RelatedDocument", mappedBy="passation", cascade={"remove"})
     */
    private $relatedDocuments;

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public static function getCategoriesChoiceToString($choice = null)
    {
        $choices = self::getCategoriesChoice();

        if (null === $choice) {
            return $choices;
        } elseif (array_key_exists($choice, $choices)) {
            return $choices[$choice];
        }

        return '';
    }

    /**
     * Set categorieID.
     *
     * @param int $categorieID
     *
     * @return Passation
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
     * @return Passation
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
     * @return Passation
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
     * Add relatedDocuments.
     *
     * @return Passation
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
