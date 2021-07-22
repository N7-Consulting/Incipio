<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Personne;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Personne\PersonneRepository")
 */
class Personne extends Adressable implements AnonymizableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="sexe", type="string", length=15, nullable=true)
     */
    private $sexe;

    /**
     * @var string
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="mobile", type="string", length=31, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="fix", type="string", length=31, nullable=true)
     */
    private $fix;

    /**
     * @var string
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="email", type="string", length=63, nullable=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="emailestvalide", type="boolean", nullable=false, options={"default" = true})
     */
    private $emailEstValide;

    /**
     * @var bool
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="estabonnenewsletter", type="boolean", nullable=false, options={"default" = true})
     */
    private $estAbonneNewsletter;

    /**
     * @var Employe
     *
     * @Groups({"gdpr"})
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Personne\Employe", mappedBy="personne", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE" )
     */
    private $employe;

    /**
     * @var User
     *
     * @Groups({"gdpr"})
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User\User", mappedBy="personne", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Membre
     *
     * @Groups({"gdpr"})
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Personne\Membre", mappedBy="personne", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $membre;

    // pour afficher Prénom Nom
    // Merci de ne pas supprimer
    public function getPrenomNom()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getNomFormel()
    {
        return $this->sexe . ' ' . mb_strtoupper($this->nom, 'UTF-8') . ' ' . $this->prenom;
    }

    public function getPoste()
    {
        if ($this->getEmploye()) {
            return $this->getEmploye()->getPoste();
        } elseif ($this->getMembre()) {  //Renvoi le plus haut poste (par id)
            $mandatValid = null;
            if (count($mandats = $this->getMembre()->getMandats())) {
                $id = 100;
                foreach ($mandats as $mandat) {
                    if ($mandat->getPoste()->getId() < $id) {
                        $mandatValid = $mandat;
                    }
                    $id = $mandat->getPoste()->getId();
                }
            }
            if ($mandatValid) {
                return $mandatValid->getPoste()->getIntitule();
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function anonymize(): void
    {
        parent::anonymize();

        $this->prenom = 'a';
        $this->nom = 'nonyme';
        $this->sexe = null;
        $this->mobile = null;
        $this->fix = null;
        $this->email = null;
        $this->emailEstValide = false;
        $this->estAbonneNewsletter = false;

        if (null !== $this->employe) {
            $this->employe->anonymize();
        }
        if (null !== $this->membre) {
            $this->membre->anonymize();
        }
        if (null !== $this->user) {
            $this->setUser(null);
        }
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
     * Set prenom.
     *
     * @param string $prenom
     *
     * @return Personne
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Personne
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set sexe.
     *
     * @param string $sexe
     *
     * @return Personne
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe.
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set mobile.
     *
     * @param string $mobile
     *
     * @return Personne
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile.
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set fix.
     *
     * @param string $fix
     *
     * @return Personne
     */
    public function setFix($fix)
    {
        $this->fix = $fix;

        return $this;
    }

    /**
     * Get fix.
     *
     * @return string
     */
    public function getFix()
    {
        return $this->fix;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Personne
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set employe.
     *
     * @param Employe $employe
     *
     * @return Personne
     */
    public function setEmploye(Employe $employe = null)
    {
        $this->employe = $employe;

        return $this;
    }

    /**
     * Get employe.
     *
     * @return Employe
     */
    public function getEmploye()
    {
        return $this->employe;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Personne
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set membre.
     *
     * @param Membre $membre
     *
     * @return Personne
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
     * Set emailEstValide.
     *
     * @param bool $emailEstValide
     *
     * @return Personne
     */
    public function setEmailEstValide($emailEstValide)
    {
        $this->emailEstValide = $emailEstValide;

        return $this;
    }

    /**
     * Get emailEstValide.
     *
     * @return bool
     */
    public function getEmailEstValide()
    {
        return $this->emailEstValide;
    }

    /**
     * Set estAbonneNewsletter.
     *
     * @param bool $estAbonneNewsletter
     *
     * @return Personne
     */
    public function setEstAbonneNewsletter($estAbonneNewsletter)
    {
        $this->estAbonneNewsletter = $estAbonneNewsletter;

        return $this;
    }

    /**
     * Get estAbonneNewsletter.
     *
     * @return bool
     */
    public function getEstAbonneNewsletter()
    {
        return $this->estAbonneNewsletter;
    }

    public function getGenre()
    {
        return $this->sexe == 'Madame' ? 'e' : '';
    }

    public function getPronom()
    {
        return $this->sexe == 'Madame' ? 'elle' : 'il';
    }

    public function getArticle() {
        return $this->sexe == 'Madame' ? 'une' : 'un';
    }

    public function __toString()
    {
        return $this->getPrenom() . ' ' . $this->getNom();
    }
}
