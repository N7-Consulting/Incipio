<?php

namespace App\Entity\Processus;

use App\Repository\Processus\ProcessusRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Publish\RelatedDocument;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Entity\Personne\Personne;

/**
 * @ORM\Entity(repositoryClass=ProcessusRepository::class)
 */
class Processus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Personne", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pilote;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Publish\RelatedDocument", mappedBy="processus", cascade={"remove"})
     */
    private $relatedDocuments;
    

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get pilote.
     *
     * @return Personne
     */
    public function getPilote()
    {
        return $this->pilote;
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
     * Set description.
     *
     * @param string $description
     *
     * @return Processus
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set pilote.
     *
     * @param Personne $pilote
     *
     * @return Processus
     */
    public function setPilote(?Personne $pilote)
    {
        $this->pilote = $pilote;

        return $this;
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
