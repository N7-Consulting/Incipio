<?php

namespace App\Entity\Hr;

use App\Repository\Hr\AlumnusContactRepository;
use App\Entity\Comment\Thread;
use App\Entity\Personne\Personne;
use App\Entity\Hr\Alumnus;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AlumnusContactRepository::class)
 */
class AlumnusContact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Alumnus
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Hr\Alumnus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $alumnus;

    /**
     * @var Personne
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Personne")
     * @ORM\JoinColumn(nullable=false)
     */
    private $faitPar;

    // /**
    //  * @var Thread
    //  *
    //  * @ORM\OneToOne(targetEntity="App\Entity\Comment\Thread",cascade={"persist"})
    //  * @ORM\JoinColumn(nullable=true)
    //  */
    // private $thread;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contenu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moyenContact;


    public function __construct()
    {
        $this->date = new \DateTime('now');
    }

    /**
     * Set faitPar.
     *
     * @return AlumnusContact
     */
    public function setFaitPar(Personne $faitPar)
    {
        $this->faitPar = $faitPar;

        return $this;
    }

    /**
     * Get faitPar.
     *
     * @return Personne
     */
    public function getFaitPar()
    {
        return $this->faitPar;
    }

    /**
     * Get alumnus.
     *
     * @return Alumnus
     */
    public function getAlumnus()
    {
        return $this->alumnus;
    }
    /**
     * Set alumnus.
     *
     * @return AlumnusContact
     */
    public function setAlumnus(Alumnus $alumnus)
    {
        $this->alumnus = $alumnus;

        return $this;
    }

    /**
     * Set thread.
     *
     * @return ClientContact
     */
    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread.
     *
     * @return Thread
     */
    public function getThread()
    {
        return $this->thread;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(?string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getMoyenContact(): ?string
    {
        return $this->moyenContact;
    }

    public function setMoyenContact(?string $moyenContact): self
    {
        $this->moyenContact = $moyenContact;

        return $this;
    }
}
