<?php

namespace App\Entity\Project;

use App\Entity\Personne\Prospect;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Project\CcaRepository")
 * @UniqueEntity("nom")
 */
class Cca extends DocType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @ORM\OneToMany(targetEntity=Etude::class, mappedBy="cca")
     */
    private $etudes;

    /**
     * @Assert\Valid()
     * @ORM\ManyToOne(targetEntity=Prospect::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $prospect;

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
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private $nom;


    public function __toString()
    {
        return $this->getNom() . ' - ' . $this->dateFin->format('d/m/Y');
    }

    public function __construct()
    {
        $this->etudes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * @return Collection|Etude[]
     */
    public function getEtude(): Collection
    {
        return $this->etudes;
    }

    public function addEtude(Etude $etude): self
    {
        if (!$this->etudes->contains($etude)) {
            $this->etudes[] = $etude;
            $etude->setCca($this);
        }

        return $this;
    }

    public function removeEtude(Etude $etude): self
    {
        if ($this->etudes->removeElement($etude)) {
            // set the owning side to null (unless already changed)
            if ($etude->getCca() === $this) {
                $etude->setCca(null);
            }
        }

        return $this;
    }

    public function getProspect(): ?Prospect
    {
        return $this->prospect;
    }

    public function setProspect(?Prospect $prospect): self
    {
        $this->prospect = $prospect;

        return $this;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
