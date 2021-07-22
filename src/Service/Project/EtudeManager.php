<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Project;

use App\Entity\Project\Ce;
use App\Entity\Project\Etude;
use Doctrine\Common\Persistence\ObjectManager;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class EtudeManager
{
    protected $em;

    protected $tva;

    protected $namingConvention;

    protected $anneeCreation;

    protected $defaultFraisDossier;

    protected $defaultPourcentageAcompte;

    public function __construct(ObjectManager $em, KeyValueStore $keyValueStore)
    {
        $this->em = $em;
        if ($keyValueStore->exists('tva')) {
            $this->tva = $keyValueStore->get('tva');
        } else {
            throw new \LogicException('Parameter TVA is undefined.');
        }

        if ($keyValueStore->exists('namingConvention')) {
            $this->namingConvention = $keyValueStore->get('namingConvention');
        } else {
            $this->namingConvention = 'id';
        }

        if ($keyValueStore->exists('anneeCreation')) {
            $this->anneeCreation = intval($keyValueStore->get('anneeCreation'));
        } else {
            throw new \LogicException('Parameter Année Creation is undefined.');
        }

        if ($keyValueStore->exists('fraisDossierDefaut')) {
            $this->defaultFraisDossier = $keyValueStore->get('fraisDossierDefaut');
        } else {
            throw new \LogicException('Parameter Frais Dossier Defaut is undefined.');
        }

        if ($keyValueStore->exists('pourcentageAcompteDefaut')) {
            $this->defaultPourcentageAcompte = $keyValueStore->get('pourcentageAcompteDefaut');
        } else {
            throw new \LogicException('Parameter Pourcentage Acompte Defaut is undefined.');
        }
    }

    /**
     * Get référence du document
     * Params : Etude $etude, mixed $doc, string $type (the type of doc).
     *
     * @param $type
     * @param int $key
     *
     * @return string
     */
    public function getRefDoc(Etude $etude, $type, $key = -1)
    {
        $type = strtoupper($type);
        $name = $etude->getReference($this->namingConvention);
        if ('AP' == $type) {
            if ($etude->getAp()) {
                return $name . '-' . $type . '-' . $etude->getAp()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ('CC' == $type) {
            if ($etude->getCc()) {
                return $name . '-' . $type . '-' . $etude->getCc()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ('RM' == $type || 'DM' == $type) {
            if ($key < 0) {
                return $name . '-' . $type;
            }
            if (
                !$etude->getMissions()->get($key)
                || !$etude->getMissions()->get($key)->getIntervenant()
            ) {
                return $name . '-' . $type . '- ERROR GETTING DEV ID - ERROR GETTING VERSION';
            } else {
                return $name . '-' . $type . '-' . $etude->getMissions()->get($key)->getIntervenant()->getIdentifiant() . '-' . $etude->getMissions()->get($key)->getVersion();
            }
        } elseif ('FA' == $type) {
            return $name . '-' . $type;
        } elseif ('FI' == $type) {
            return $name . '-' . $type . ($key + 1);
        } elseif ('FS' == $type) {
            return $name . '-' . $type;
        } elseif (\App\Controller\Publish\TraitementController::DOCTYPE_PROCES_VERBAL_INTERMEDIAIRE == $type) {
            if ($key >= 0 && $etude->getPvis($key)) {
                return $name . '-' . $type . ($key + 1) . '-' . $etude->getPvis($key)->getVersion();
            } else {
                return $name . '-' . $type . ($key + 1) . '- ERROR GETTING PVRI';
            }
        } elseif (\App\Controller\Publish\TraitementController::DOCTYPE_PROCES_VERBAL_FINAL == $type) {
            if ($etude->getPvr()) {
                return $name . '-' . $type . '-' . $etude->getPvr()->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } elseif ('CE' == $type) {
            if (
                !$etude->getMissions()->get($key)
                || !$etude->getMissions()->get($key)->getIntervenant()
            ) {
                return $etude->getMandat() . '-CE- ERROR GETTING DEV ID';
            } else {
                $identifiant = $etude->getMissions()->get($key)->getIntervenant()->getIdentifiant();
            }

            return $etude->getMandat() . '-CE-' . $identifiant;
        } elseif ('AVCC' == $type) {
            if ($etude->getCc() && $etude->getAvs()->get($key)) {
                return $name . '-CC-' . $etude->getCc()->getVersion() . '-AV' . ($key + 1) . '-' . $etude->getAvs()->get($key)->getVersion();
            } else {
                return $name . '-' . $type . '- ERROR GETTING VERSION';
            }
        } else {
            return 'ERROR';
        }
    }

    /**
     * Get nouveau numéro d'etude, pour valeur par defaut dans formulaire.
     */
    public function getNouveauNumero()
    {
        $mandat = $this->getMaxMandat();
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.num')
            ->from(Etude::class, 'e')
            ->andWhere('e.mandat = :mandat')
            ->setParameter('mandat', $mandat)
            ->orderBy('e.num', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value['num']) {
            return $value['num'] + 1;
        } else {
            return;
        }
    }

    /**
     * Get frais de dossier par défaut.
     */
    public function getDefaultFraisDossier()
    {
        return $this->defaultFraisDossier;
    }

    /**
     * Get pourcentage d'acompte par défaut.
     */
    public function getDefaultPourcentageAcompte()
    {
        return $this->defaultPourcentageAcompte;
    }

    /**
     * Converti le numero de mandat en année.
     *
     * @param $idMandat
     *
     * @return string
     */
    public function mandatToString($idMandat)
    {
        return strval($this->anneeCreation + $idMandat) . '/' . strval($this->anneeCreation + 1 + $idMandat);
    }

    /**
     * Get le maximum des mandats.
     */
    public function getMaxMandat()
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.mandat')
            ->from(Etude::class, 'e')
            ->orderBy('e.mandat', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value) {
            return $value['mandat'];
        } else {
            return date('Y') - $this->anneeCreation;
        }
    }

    /**
     * Get le minimum des mandats.
     */
    public function getMinMandat()
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.mandat')
            ->from(Etude::class, 'e')
            ->orderBy('e.mandat', 'ASC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value) {
            return $value['mandat'];
        } else {
            return 0;
        }
    }

    /**
     * Get le maximum des mandats par rapport à la date de Signature de signature des CE.
     */
    public function getmaxMandatCe()
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('c.dateSignature')
            ->from(Ce::class, 'c')
            ->orderBy('c.dateSignature', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();

        if ($value) {
            return intval($value['dateSignature']->format('Y'));
        } else {
            return 0;
        }
    }

    /**
     * Taux de conversion.
     */
    public function getTauxConversion()
    {
        $tauxConversion = [];

        //recup toute les etudes
        $etudes = $this->em->getRepository(Etude::class)->findAll();
        foreach ($etudes as $etude) {
            $mandat = $etude->getMandat();
            if (null !== $etude->getAp()) {
                if ($etude->getAp()->getSpt2()) {
                    if (isset($tauxConversion[$mandat])) {
                        $ApRedige = $tauxConversion[$mandat]['ap_redige'];
                        ++$ApRedige;
                        $ApSigne = $tauxConversion[$mandat]['ap_signe'];
                        ++$ApSigne;
                    } else {
                        $ApRedige = 1;
                        $ApSigne = 1;
                    }
                    $tauxConversionCalc = ['mandat' => $mandat, 'ap_redige' => $ApRedige, 'ap_signe' => $ApSigne];
                    $tauxConversion[$mandat] = $tauxConversionCalc;
                } elseif ($etude->getAp()->getRedige()) {
                    if (isset($tauxConversion[$mandat])) {
                        $ApRedige = $tauxConversion[$mandat]['ap_redige'];
                        ++$ApRedige;
                        $ApSigne = $tauxConversion[$mandat]['ap_signe'];
                    } else {
                        $ApRedige = 1;
                        $ApSigne = 0;
                    }
                    $tauxConversionCalc = ['mandat' => $mandat, 'ap_redige' => $ApRedige, 'ap_signe' => $ApSigne];
                    $tauxConversion[$mandat] = $tauxConversionCalc;
                }
            }
        }

        return $tauxConversion;
    }
}
