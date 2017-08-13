<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\SuiviBundle\Form\Type;

use Mgate\SuiviBundle\Entity\Av;
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
        $builder->add('differentielDelai', IntegerType::class, ['label' => 'Modification du Délai (+/- x jours)', 'required' => true])
        ->add('objet', TextareaType::class, ['label' => 'Exposer les causes de l’Avenant. Ne pas hésiter à 
        détailler l\'historique des relations avec le client et du travail sur l\'étude qui ont conduit à l\'Avenant.',
        'required' => true, ])
        ->add('clauses', ChoiceType::class, ['label' => 'Type d\'avenant', 'multiple' => true, 'choices' => Av::getClausesChoices(),
            ])
        ->add('phases', CollectionType::class, [
                'entry_type' => PhaseType::class,
                'entry_options' => ['isAvenant' => true],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                ]);
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
        return 'Mgate_suivibundle_avtype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Mgate\SuiviBundle\Entity\Av',
            'prospect' => '',
        ]);
    }
}
