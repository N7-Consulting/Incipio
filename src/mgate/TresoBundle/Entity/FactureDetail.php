<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mgate\TresoBundle\Entity\Compte;
use mgate\TresoBundle\Entity\Facture;

/**
 * FactureDetail.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FactureDetail
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
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="details", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $facture;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="montantHT", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $montantHT;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxTVA", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $tauxTVA;

    /**
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumn(nullable=true)
     */
    private $compte;

    /**
     * ADDITIONAL.
     */
    public function getMontantTVA()
    {
        return $this->tauxTVA * $this->montantHT / 100;
    }

    public function getMontantTTC()
    {
        return $this->montantHT + $this->getMontantTVA();
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
     * Set description.
     *
     * @param string $description
     *
     * @return FactureDetail
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
     * Set montantHT.
     *
     * @param float $montantHT
     *
     * @return FactureDetail
     */
    public function setMontantHT($montantHT)
    {
        $this->montantHT = $montantHT;

        return $this;
    }

    /**
     * Get montantHT.
     *
     * @return float
     */
    public function getMontantHT()
    {
        return $this->montantHT;
    }

    /**
     * Set tauxTVA.
     *
     * @param float $tauxTVA
     *
     * @return FactureDetail
     */
    public function setTauxTVA($tauxTVA)
    {
        $this->tauxTVA = $tauxTVA;

        return $this;
    }

    /**
     * Get tauxTVA.
     *
     * @return float
     */
    public function getTauxTVA()
    {
        return $this->tauxTVA;
    }

    /**
     * Set compte.
     *
     * @param Compte $compte
     *
     * @return FactureDetail
     */
    public function setCompte(Compte $compte = null)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte.
     *
     * @return Compte
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set facture.
     *
     * @param Facture $facture
     *
     * @return FactureDetail
     */
    public function setFacture(Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture.
     *
     * @return Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }
}
