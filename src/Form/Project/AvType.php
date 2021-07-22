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

use App\Entity\Project\Av;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvType extends DocTypeType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', IntegerType::class, [
                'label' => 'Numéro de l\'avenant',
                'required' => true,
            ])
            ->add('differentielDelai', IntegerType::class, [
            'label' => 'Modification du Délai (+/- x jours)',
            'required' => true,
            ])
            ->add('objet', TextareaType::class, [
                'label' => 'Exposer les causes de l’Avenant',
                'required' => true,
                'attr' => ['data-help' => 'Ne pas hésiter à détailler l\'historique des relations avec le client 
            et du travail sur l\'étude qui ont conduit à l\'Avenant',
                ],
            ])
            ->add('clauses', ChoiceType::class, [
                'label' => 'Type d\'avenant',
                'multiple' => true,
                'choices' => Av::CLAUSES_CHOICES,
            ]);
            // ->add('phases', CollectionType::class, [
            //     'entry_type' => PhaseType::class,
            //     'entry_options' => ['isAvenant' => true],
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'prototype' => true,
            //     'by_reference' => false,
            // ]);
        /*->add('avenantsMissions', 'collection', array(
            'type' => new AvMissionType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false,
        ))*/

        DocTypeType::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'project_avtype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Av::class,
            'prospect' => '',
        ]);
    }
}
