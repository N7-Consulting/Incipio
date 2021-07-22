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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Publish\DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    const DOCUMENT_STORAGE_ROOT = '/var/documents'; //document storage root on kernerl->getProjectDir() path.

    const DOCUMENT_TMP_FOLDER = '/public/tmp'; // tmp folder in web directory on kernerl->getProjectDir() path.

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="RelatedDocument", inversedBy="document", cascade={"all"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $relation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uptime", type="datetime")
     */
    private $uptime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne\Personne", cascade={"persist"})
     * @ORM\JoinColumn(name="author_personne_id", referencedColumnName="id", nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var string
     * @Assert\NotBlank
     * Defined on creation.
     */
    private $projectDir;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pole;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    public function __toString()
    {
        return 'Document ' . $this->getId() . ' ' . $this->path;
    }

    /**
     * @return string|null
     */
    public function getAbsolutePath()
    {
        if (!empty($this->projectDir)) {
            return $this->projectDir . '' . self::DOCUMENT_STORAGE_ROOT . '/' . $this->path;
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->file->guessExtension();
            $this->size = filesize($this->file);
        }
        $this->uptime = new \DateTime();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        if (empty($this->projectDir)) {
            throw new \Exception('$this->projectDir is undefined');
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        // moving file into /data
        $this->file->move($this->projectDir . '' . self::DOCUMENT_STORAGE_ROOT, $this->path);
        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Set path.
     *
     * @param $path
     *
     * @return Document
     */
    public function setSubdirectory($path)
    {
        $this->subdirectory = $path;

        return $this;
    }

    /** auto generated methods */

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Document
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param mixed $relation
     *
     * @return Document
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     *
     * @return Document
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * @param \DateTime $uptime
     *
     * @return Document
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     *
     * @return Document
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     *
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return Document
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getProjectDir()
    {
        return $this->projectDir;
    }

    /**
     * @param string $projectDir
     *
     * @return Document
     */
    public function setProjectDir($projectDir)
    {
        $this->projectDir = $projectDir;

        return $this;
    }

    public function getPole(): ?string
    {
        return $this->pole;
    }

    public function setPole(?string $pole): self
    {
        $this->pole = $pole;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
