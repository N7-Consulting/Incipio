<?php

namespace App\Entity\Project;

use App\Entity\Personne\Personne;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Project\BdcRepository")
 */
class Bdc extends DocType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Etude::class, mappedBy="bdc")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $etude;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrDev;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class)
     */
    private $contact;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deonto;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtude(): ?Etude
    {
        return $this->etude;
    }

    public function setEtude(Etude $etude): self
    {
        $this->etude = $etude;

        return $this;
    }

    public function getNbrDev(): ?int
    {
        return $this->nbrDev;
    }

    public function setNbrDev(?int $nbrDev): self
    {
        $this->nbrDev = $nbrDev;

        return $this;
    }

    public function getContact(): ?Personne
    {
        return $this->contact;
    }

    public function setContact(?Personne $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDeonto(): ?bool
    {
        return $this->deonto;
    }

    public function setDeonto(?bool $deonto): self
    {
        $this->deonto = $deonto;

        return $this;
    }
}
