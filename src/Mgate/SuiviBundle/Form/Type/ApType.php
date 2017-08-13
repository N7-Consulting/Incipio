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

use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Mgate\PersonneBundle\Entity\PersonneRepository;
use Mgate\SuiviBundle\Entity\Etude;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('suiveur', Select2EntityType::class, ['label' => 'Suiveur de projet',
            'class' => 'Mgate\\PersonneBundle\\Entity\\Personne',
            'choice_label' => 'prenomNom',
            'query_builder' => function (PersonneRepository $pr) {
                return $pr->getByMandatNonNulQueryBuilder();
            },
            'required' => false, ])
            ->add('ap', SubApType::class, ['label' => ' ', 'prospect' => $options['prospect']])
            ->add('fraisDossier', IntegerType::class, ['label' => 'Frais de dossier', 'required' => false])
            ->add('presentationProjet', TextareaType::class,
                ['label' => 'Présentation du projet',
                    'required' => false,
                    'attr' => ['cols' => '100%', 'rows' => 5], ]
            )
            ->add('descriptionPrestation', TextareaType::class,
                ['label' => 'Description de la prestation proposée',
                    'required' => false,
                    'attr' => ['title' => "La phrase commence par 'N7 Consulting réalisera, pour le compte du Client, 
                    une étude consistant en'. Il faut la continuer en décrivant la prestation proposée. 
                    Le début de la phrase est déjà généré.",
                        'cols' => '100%',
                        'rows' => 5, ], ])
            ->add('typePrestation', ChoiceType::class,
                ['choices' => array_flip(Etude::getTypePrestationChoice()),
                    'label' => 'Type de prestation',
                    'required' => false,
                    ])
            ->add('competences', Select2EntityType::class, [
                'class' => 'N7consulting\RhBundle\Entity\Competence',
                'by_reference' => false,
                'multiple' => true,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'Mgate_suivibundle_aptype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Mgate\SuiviBundle\Entity\Etude',
            'prospect' => '',
        ]);
    }
}
