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

use App\Entity\Project\Ap;
use App\Entity\Project\Cc;
use App\Entity\Project\Ce;
use App\Entity\Project\Cca;
use App\Entity\Project\Bdc;
use App\Entity\Project\Etude;
use App\Entity\Project\ProcesVerbal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuiviEtudeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'stateID',
                ChoiceType::class,
                [
                    'choices' => array_flip(Etude::ETUDE_STATE_ARRAY),
                    'translation_domain' => 'project',
                    'required' => true,
                ]
            )
            ->add(
                'stateDescription',
                TextareaType::class,
                [
                    'label' => 'suivi.avance_etude',
                    'translation_domain' => 'project',
                    'required' => false,
                    'attr' => ['cols' => '100%', 'rows' => 5]
                ]
            )
            ->add(
                'ap',
                DocTypeSuiviType::class,
                [
                    'label' => false,
                    'translation_domain' => 'project',
                    'data_class' => Ap::class
                ]
            )
            ->add(
                'cc',
                DocTypeSuiviType::class,
                [
                    'label' => false,
                    'translation_domain' => 'project',
                    'data_class' => Cc::class
                ]
            )
            ->add(
                'ce',
                DocTypeSuiviType::class,
                [
                    'label' => false,
                    'translation_domain' => 'project',
                    'data_class' => Ce::class
                ]
            )
            ->add(
                'cca',
                DocTypeSuiviType::class,
                [
                    'label' => false,
                    'translation_domain' => 'project',
                    'data_class' => Cca::class
                ]
            )
            ->add(
                'bdc',
                DocTypeSuiviType::class,
                [
                    'label' => false,
                    'translation_domain' => 'project',
                    'data_class' => Bdc::class
                ]
            );
        $builder->add(
            'missions',
            CollectionType::class,
            [
                'entry_type' => DocTypeSuiviType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
            ]
        );
        $builder->add(
            'pvis',
            CollectionType::class,
            [
                'entry_type' => DocTypeSuiviType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
            ]
        );
        $builder->add(
            'avs',
            CollectionType::class,
            [
                'label' => false,
                'entry_type' => DocTypeSuiviType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
            ]
        );
        $builder->add(
            'pvr',
            DocTypeSuiviType::class,
            [
                'label' => false,
                'data_class' => ProcesVerbal::class
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'project_suivietudetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etude::class,
        ]);
    }
}
