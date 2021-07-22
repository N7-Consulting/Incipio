<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Personne;

use App\Entity\Hr\Competence;
use App\Entity\Personne\Filiere;
use App\Entity\Personne\Membre;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2CountryType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('personne', PersonneType::class, ['label' => ' ', 'user' => true])
            ->add(
                'identifiant',
                TextType::class,
                ['label' => 'Identifiant',
                 'required' => false,
                 'attr' => ['readonly' => true],
                ]
            )
            ->add('emailEMSE', TextType::class, ['label' => 'Email Ecole', 'required' => false])
            ->add('promotion', IntegerType::class, ['label' => 'Promotion', 'required' => false])
            ->add(
                'dateDeNaissance',
                DateType::class,
                [
                    'label' => 'Date de naissance',
                    'widget' => 'single_text',
                    'required' => false,
                ]
            )
            ->add('lieuDeNaissance', TextType::class, ['label' => 'Lieu de naissance', 'required' => false])
            ->add(
                'nationalite',
                Select2CountryType::class,
                ['label' => 'Nationalité', 'required' => true, 'preferred_choices' => ['FR']]
            )
            ->add('mandats', CollectionType::class, [
                'entry_type' => MandatType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
            ])
            ->add('competences', Select2EntityType::class, [
                'class' => Competence::class,
                'by_reference' => false,
                'multiple' => true,
                'required' => false,
            ])
            ->add(
                'dateConventionEleve',
                DateType::class,
                [
                    'label' => 'Date de Signature de la Convention Elève',
                    'required' => false,
                    'widget' => 'single_text',
                ]
            )
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Modifier la photo de profil du membre',
            ])
            ->add(
                'formatPaiement',
                ChoiceType::class,
                ['choices' => ['aucun' => 'aucun', 'cheque' => 'Chèque', 'especes' => 'Espèces'], 'required' => true]
            )
            ->add(
                'filiere',
                EntityType::class,
                ['label' => 'Filiere',
                 'class' => Filiere::class,
                 'required' => true,
                ]
            )
            ->add(
                'securiteSociale',
                TextType::class,
                ['required' => false, 'label' => 'Numéro de sécurité sociale']
            )
            ->add(
                'commentaire',
                TextareaType::class,
                ['required' => false, 'label' => 'Commentaire libre']
            );
    }

    public function getBlockPrefix()
    {
        return 'personne_membretype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
