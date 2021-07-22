<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Treso;

use App\Entity\Personne\Prospect;
use App\Entity\Project\Etude;
use App\Entity\Treso\Facture;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('exercice', IntegerType::class, ['label' => 'Exercice Comptable', 'required' => true])
            ->add('numero', IntegerType::class, ['label' => 'Numéro de la Facture', 'required' => true])
            ->add('type', ChoiceType::class, ['choices' => array_flip(Facture::getTypeChoices()),
                                              'required' => true,
            ])
            ->add(
                'objet',
                TextareaType::class,
                ['label' => 'Objet de la Facture',
                 'required' => true,
                 'attr' => [
                     'cols' => '100%',
                     'rows' => 5,
                 ],
                ]
            )
            ->add('etude', Select2EntityType::class, [
                'label' => 'Etude',
                'class' => Etude::class,
                'choice_label' => 'nom',
                ])
            ->add('details', CollectionType::class, [
                'entry_type' => FactureDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ])
            ->add('beneficiaire', Select2EntityType::class, [
                'class' => Prospect::class,
                'choice_label' => 'nom',
                'required' => true,
                'label' => 'Facture émise pour/par',
            ])
            ->add('montantADeduire', FactureDetailType::class, ['label' => 'Montant à déduire', 'required' => true])
            ->add(
                'dateEmission',
                DateType::class,
                ['label' => 'Date d\'émission', 'required' => true, 'widget' => 'single_text']
            )
            ->add(
                'dateVersement',
                DateType::class,
                ['label' => 'Date de versement', 'required' => false, 'widget' => 'single_text']
            );
    }

    public function getBlockPrefix()
    {
        return 'treso_facturetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
