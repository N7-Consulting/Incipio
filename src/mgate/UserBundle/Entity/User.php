<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src/mgate/UserBundle/Entity/User.php


namespace mgate\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Yaml\Parser;

/**
 * @ORM\Entity(repositoryClass="mgate\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="\mgate\PersonneBundle\Entity\Personne", inversedBy="user", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $personne;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
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
     * Set personne.
     *
     * @param \mgate\PersonneBundle\Entity\Personne $personne
     *
     * @return User
     */
    public function setPersonne(\mgate\PersonneBundle\Entity\Personne $personne = null)
    {
        $this->personne = $personne;

        if ($personne) {
            $this->personne->setUser($this);
        }

        return $this;
    }

    /**
     * Get personne.
     *
     * @return \mgate\PersonneBundle\Entity\Personne
     */
    public function getPersonne()
    {
        return $this->personne;
    }

    private static function convertRoleToLabel($role)
    {
        $roleDisplay = str_replace('ROLE_', '', $role);
        $roleDisplay = str_replace('_', ' ', $roleDisplay);

        return ucwords(strtolower($roleDisplay));
    }

    /** pour afficher les roles
     * Get getRolesDisplay.
     *
     * @return string
     */
    public function getRolesDisplay()
    {
        $rolesArray = $this->getRoles();

        $liste = '';
        foreach ($rolesArray as $role) {
            $liste .= ' ' . self::convertRoleToLabel($role);
        }

        return $liste;
    }
}
