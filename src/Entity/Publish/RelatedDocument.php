<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Publish;

use App\Entity\Formation\Formation;
use App\Entity\Personne\Membre;
use App\Entity\Processus\Processus;
use App\Entity\Formation\Passation;
use App\Entity\Personne\Prospect;
use App\Entity\Project\Etude;
use Doctrine\ORM\Mapping as ORM;

/**
 * A related document is a join table between a document and objects in the application.
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RelatedDocument
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Document", mappedBy="relation", cascade={"persist", "merge"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $document;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Membre", inversedBy="relatedDocuments",
     *                                                                   cascade={"persist"})
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id", nullable=true)
     */
    private $membre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project\Etude", inversedBy="relatedDocuments", cascade={"persist"})
     * @ORM\JoinColumn(name="etude_id", referencedColumnName="id", nullable=true)
     */
    private $etude;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Formation\Formation", cascade={"persist"}, inversedBy="relatedDocuments")
     * @ORM\JoinColumn(name="formation_id", referencedColumnName="id", nullable=true)
     */
    private $formation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Formation\Passation", cascade={"persist"}, inversedBy="relatedDocuments")
     * @ORM\JoinColumn(name="passation_id", referencedColumnName="id", nullable=true)
     */
    private $passation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Prospect", cascade={"persist"})
     * @ORM\JoinColumn(name="prospect_id", referencedColumnName="id", nullable=true)
     */
    private $prospect;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Processus\Processus", cascade={"persist"}, inversedBy="relatedDocuments")
     * @ORM\JoinColumn(name="processus_id", referencedColumnName="id", nullable=true)
     */
    private $processus;

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
     * Set document.
     *
     * @param Document $document
     *
     * @return RelatedDocument
     */
    public function setDocument(Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document.
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set membre.
     *
     * @param Membre $membre
     *
     * @return RelatedDocument
     */
    public function setMembre(Membre $membre = null)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre.
     *
     * @return Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Set etude.
     *
     * @param Etude $etude
     *
     * @return RelatedDocument
     */
    public function setEtude(Etude $etude = null)
    {
        $this->etude = $etude;

        return $this;
    }

    /**
     * Get etude.
     *
     * @return Etude
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set formation.
     *
     * @param Formation $formation
     *
     * @return RelatedDocument
     */
    public function setFormation(Formation $formation = null)
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * Get formation.
     *
     * @return Formation
     */
    public function getFormation()
    {
        return $this->formation;
    }

    /**
     * Set prospect.
     *
     * @param Prospect $prospect
     *
     * @return RelatedDocument
     */
    public function setProspect(Prospect $prospect = null)
    {
        $this->prospect = $prospect;

        return $this;
    }

    /**
     * Get prospect.
     *
     * @return Prospect
     */
    public function getProspect()
    {
        return $this->prospect;
    }

    /**
     * Set processus.
     *
     * @param Processus $processus
     *
     * @return RelatedDocument
     */
    public function setProcessus(Processus $processus = null)
    {
        $this->processus = $processus;

        return $this;
    }

    /**
     * Get processus.
     *
     * @return Processus
     */
    public function getProcessus()
    {
        return $this->processus;
    }

    /**
     * Set passation.
     *
     * @param Passation $passation
     *
     * @return RelatedDocument
     */
    public function setPassation(Passation $passation = null)
    {
        $this->passation = $passation;

        return $this;
    }

    /**
     * Get passation.
     *
     * @return Passation
     */
    public function getPassation()
    {
        return $this->passation;
    }

    public function __toString()
    {
        return 'RelatedDocument ' . $this->getId();
    }
}
