<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\FormationBundle\Form\Type;

use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2ChoiceType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Mgate\FormationBundle\Entity\Formation;
use Mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', TextType::class, ['label' => 'Titre de la formation', 'required' => true])
                ->add('description', TextareaType::class, ['label' => 'Description de la Formation', 'required' => true, 'attr' => ['cols' => '100%', 'rows' => 5]])
                ->add('categorie', Select2ChoiceType::class, [
                    'multiple' => true,
                    'choices' => array_flip(Formation::getCategoriesChoice()),
                    'label' => 'Catégorie',
                    'required' => false, ]
                )
                ->add('dateDebut', DateTimeType::class, ['label' => 'Date de debut', 'format' => 'd/MM/y - HH:mm', 'required' => true, 'widget' => 'choice'])
                ->add('dateFin', DateTimeType::class, ['label' => 'Date de fin', 'format' => 'd/MM/y - HH:mm', 'required' => true, 'widget' => 'choice'])
                ->add('mandat', IntegerType::class)
                ->add('formateurs', CollectionType::class, [
                    'entry_type' => Select2EntityType::class,
                    'entry_options' => ['label' => 'Suiveur de projet',
                        'class' => 'Mgate\\PersonneBundle\\Entity\\Personne',
                        'choice_label' => 'prenomNom',
                        'query_builder' => function (PersonneRepository $pr) {
                            return $pr->getMembreOnly();
                        },
                    'required' => false, ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('membresPresents', CollectionType::class, [
                    'entry_type' => Select2EntityType::class,
                    'entry_options' => ['label' => 'Suiveur de projet',
                        'class' => 'Mgate\\PersonneBundle\\Entity\\Personne',
                        'choice_label' => 'prenomNom',
                        'query_builder' => function (PersonneRepository $pr) {
                            return $pr->getMembreOnly();
                        },
                        'required' => false, ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('docPath', TextType::class, ['label' => 'Lien vers des documents externes', 'required' => false])
        ;
    }

    public function getBlockPrefix()
    {
        return 'Mgate_suivibundle_formulairetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Mgate\FormationBundle\Entity\Formation',
        ]);
    }
}
