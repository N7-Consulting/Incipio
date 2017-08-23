<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mgate\CommentBundle\Entity\Thread;
use Mgate\PersonneBundle\Entity\Personne;

/**
 * Mgate\SuiviBundle\Entity\ClientContact.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mgate\SuiviBundle\Entity\ClientContactRepository")
 */
class ClientContact
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="clientContacts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /** , inversedBy="clientContacts", cascade={"persist"}
     * @ORM\ManyToOne(targetEntity="Mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinColumn(nullable=false)
     */
    private $faitPar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="\Mgate\CommentBundle\Entity\Thread",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $thread;

    /**
     * @var string
     * @ORM\Column(name="objet", type="text",nullable=true)
     */
    private $objet;

    /**
     * @var string
     * @ORM\Column(name="contenu", type="text",nullable=true)
     */
    private $contenu;

    /**
     * @var text
     * @ORM\Column(name="moyenContact", type="text",nullable=true)
     */
    private $moyenContact;

    public function __construct()
    {
        $this->date = new \DateTime('now');
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
     * @param string $etude
     *
     * @return ClientContact
     */
    public function setEtude($etude)
    {
        $this->etude = $etude;

        return $this;
    }

    /**
     * Get etude.
     *
     * @return string
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set faitPar.
     *
     * @param Mgate\PersonneBundle\Entity\Personne $faitPar
     *
     * @return ClientContact
     */
    public function setFaitPar(Personne $faitPar)
    {
        $this->faitPar = $faitPar;

        return $this;
    }

    /**
     * Get faitPar.
     *
     * @return Mgate\PersonneBundle\Entity\Personne
     */
    public function getFaitPar()
    {
        return $this->faitPar;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return ClientContact
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set thread.
     *
     * @param Thread $thread
     *
     * @return Prospect
     */
    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread.
     *
     * @return Mgate\CommentBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set contenu.
     *
     * @param string $contenu
     *
     * @return ClientContact
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu.
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set mail.
     *
     * @param bool $mail
     *
     * @return ClientContact
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail.
     *
     * @return bool
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set appel.
     *
     * @param bool $appel
     *
     * @return ClientContact
     */
    public function setAppel($appel)
    {
        $this->appel = $appel;

        return $this;
    }

    /**
     * Get appel.
     *
     * @return bool
     */
    public function getAppel()
    {
        return $this->appel;
    }

    /**
     * Set moyenContact.
     *
     * @param string $moyenContact
     *
     * @return ClientContact
     */
    public function setMoyenContact($moyenContact)
    {
        $this->moyenContact = $moyenContact;

        return $this;
    }

    /**
     * Get moyenContact.
     *
     * @return string
     */
    public function getMoyenContact()
    {
        return $this->moyenContact;
    }

    /**
     * Set objet.
     *
     * @param string $objet
     *
     * @return ClientContact
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

    public function __toString()
    {
        return $this->getId() . ' - ' . $this->getFaitPar()->__toString() . ' ' . $this->getObjet();
    }
}
