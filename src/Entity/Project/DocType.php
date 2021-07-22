<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Project;

use App\Entity\Personne\Personne;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/** @ORM\MappedSuperclass */
class DocType
{
    /**
     * @var bool
     */
    private $knownSignataire2 = false;

    /**
     * @var Personne
     */
    private $newSignataire2;

    /**
     * @var int
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var bool
     *
     * @ORM\Column(name="redige", type="boolean",nullable=true)
     */
    private $redige;

    /**
     * @var bool
     *
     * @ORM\Column(name="relu", type="boolean",nullable=true)
     */
    private $relu;

    /**
     * @var bool
     *
     * @ORM\Column(name="spt1", type="boolean",nullable=true)
     */
    private $spt1;

    /**
     * @var bool
     *
     * @ORM\Column(name="spt2", type="boolean",nullable=true)
     */
    private $spt2;

    /**
     * @var Personne
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Personne", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $signataire1;

    /**
     * @var Personne
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Personne", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $signataire2;

    /**
     * @var \DateTime
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="dateSignature", type="datetime",nullable=true)
     */
    private $dateSignature;

    /**
     * @var bool
     *
     * @ORM\Column(name="envoye", type="boolean",nullable=true)
     */
    private $envoye;

    /**
     * @var bool
     *
     * @ORM\Column(name="receptionne", type="boolean",nullable=true)
     */
    private $receptionne;

    /**
     * @var int
     *
     * @ORM\Column(name="generer", type="integer",nullable=true)
     */
    private $generer;

    public function __construct()
    {
        $this->setVersion(1);
    }

    /**
     * @Groups({"gdpr"})
     *
     * @return string|null
     */
    public function getSignataire1NomFormel()
    {
        return $this->signataire1 ? $this->signataire1->getNomFormel() : null;
    }

    /**
     * @Groups({"gdpr"})
     *
     * @return string|null
     */
    public function getSignataire2NomFormel()
    {
        return $this->signataire2 ? $this->signataire2->getNomFormel() : null;
    }

    // rajout à la main
    public function isKnownSignataire2()
    {
        return $this->knownSignataire2;
    }

    public function setKnownSignataire2($boolean)
    {
        $this->knownSignataire2 = $boolean;
    }

    /**
     * @return Personne
     */
    public function getNewSignataire2()
    {
        return $this->newSignataire2;
    }

    public function setNewSignataire2($var)
    {
        $this->newSignataire2 = $var;
    }

    // fin rajout

    /**
     * Set version.
     *
     * @param int $version
     *
     * @return DocType
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set redige.
     *
     * @param bool $redige
     *
     * @return DocType
     */
    public function setRedige($redige)
    {
        $this->redige = $redige;

        return $this;
    }

    /**
     * Get redige.
     *
     * @return bool
     */
    public function getRedige()
    {
        return $this->redige;
    }

    /**
     * Set relu.
     *
     * @param bool $relu
     *
     * @return DocType
     */
    public function setRelu($relu)
    {
        $this->relu = $relu;

        return $this;
    }

    /**
     * Get relu.
     *
     * @return bool
     */
    public function getRelu()
    {
        return $this->relu;
    }

    /**
     * Set spt1.
     *
     * @param bool $spt1
     *
     * @return DocType
     */
    public function setSpt1($spt1)
    {
        $this->spt1 = $spt1;

        return $this;
    }

    /**
     * Get spt1.
     *
     * @return bool
     */
    public function getSpt1()
    {
        return $this->spt1;
    }

    /**
     * Set dateSignature.
     *
     * @param \DateTime $dateSignature
     *
     * @return DocType
     */
    public function setDateSignature($dateSignature)
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    /**
     * Get dateSignature.
     *
     * @return \DateTime
     */
    public function getDateSignature()
    {
        return $this->dateSignature;
    }

    /**
     * Set envoye.
     *
     * @param bool $envoye
     *
     * @return DocType
     */
    public function setEnvoye($envoye)
    {
        $this->envoye = $envoye;

        return $this;
    }

    /**
     * Get envoye.
     *
     * @return bool
     */
    public function getEnvoye()
    {
        return $this->envoye;
    }

    /**
     * Set receptionne.
     *
     * @param bool $receptionne
     *
     * @return DocType
     */
    public function setReceptionne($receptionne)
    {
        $this->receptionne = $receptionne;

        return $this;
    }

    /**
     * Get receptionne.
     *
     * @return bool
     */
    public function getReceptionne()
    {
        return $this->receptionne;
    }

    /**
     * Set spt2.
     *
     * @param bool $spt2
     *
     * @return DocType
     */
    public function setSpt2($spt2)
    {
        $this->spt2 = $spt2;

        return $this;
    }

    /**
     * Get spt2.
     *
     * @return bool
     */
    public function getSpt2()
    {
        return $this->spt2;
    }

    /**
     * Set signataire1.
     *
     * @param Personne $signataire1
     *
     * @return DocType
     */
    public function setSignataire1(?Personne $signataire1)
    {
        $this->signataire1 = $signataire1;

        return $this;
    }

    /**
     * Get signataire1.
     *
     * @return Personne
     */
    public function getSignataire1()
    {
        return $this->signataire1;
    }

    /**
     * Set signataire2.
     *
     * @param Personne $signataire2
     *
     * @return DocType
     */
    public function setSignataire2(?Personne $signataire2)
    {
        $this->signataire2 = $signataire2;

        return $this;
    }

    /**
     * Get signataire2.
     *
     * @return Personne
     */
    public function getSignataire2()
    {
        return $this->signataire2;
    }

    /**
     * Set generer.
     *
     * @param int $generer
     *
     * @return DocType
     */
    public function setGenerer($generer)
    {
        $this->generer = $generer;

        return $this;
    }

    /**
     * Get generer.
     *
     * @return int
     */
    public function getGenerer()
    {
        return $this->generer;
    }
}
