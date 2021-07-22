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

use App\Entity\Personne\Membre;
use App\Entity\Project\Etude;
use App\Entity\Project\Mission;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends DocTypeType
{
    protected $etude;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['etude']) || !($options['etude'] instanceof Etude)) {
            throw new \LogicException('A MissionsType can\'t be build without associated Etude object.');
        }
        $this->etude = $options['etude'];

        $builder
            ->add('intervenant', Select2EntityType::class, [
                'class' => Membre::class,
                'choice_label' => 'personne.prenomNom',
                'label' => 'Intervenant',
                'required' => true,
            ])
            ->add(
                'debutOm',
                DateType::class,
                [
                    'label' => 'Début du Récapitulatif de Mission',
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'finOm',
                DateType::class,
                [
                    'label' => 'Fin du Récapitulatif de Mission',
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'pourcentageJunior',
                PercentType::class,
                ['label' => 'Pourcentage junior', 'required' => true, 'scale' => 2, 'data' => $options['acompte']]
            )
            ->add('referentTechnique', Select2EntityType::class, [
                'class' => Membre::class,
                'choice_label' => 'personne.prenomNom',
                'label' => 'Référent Technique',
                'required' => false,
            ])
            ->add('repartitionsJEH', CollectionType::class, [
                'entry_type' => RepartitionJEHType::class,
                'entry_options' => ['etude' => $this->etude],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ]);
        DocTypeType::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'project_missiontype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
            'acompte' => '0',

        ]);
        $resolver->setRequired(['etude']);
        $resolver->addAllowedTypes('etude', Etude::class);
    }
}
