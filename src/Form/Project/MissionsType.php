<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Project;

use App\Entity\Project\Etude;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionsType extends AbstractType
{
    protected $etude;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['etude']) || !($options['etude'] instanceof Etude)) {
            throw new \LogicException('A MissionsType can\'t be build without associated Etude object.');
        }
        $this->etude = $options['etude'];

        $builder->add('missions', CollectionType::class, [
            'entry_type' => MissionType::class,
            'entry_options' => ['etude' => $this->etude, 'acompte' => $options['acompte']],
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false, //indispensable cf doc
        ]);
    }

    public function getBlockPrefix()
    {
        return 'project_missionstype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etude::class,
            'acompte' => '0',
        ]);
        $resolver->setRequired(['etude']);
        $resolver->addAllowedTypes('etude', Etude::class);
    }
}
