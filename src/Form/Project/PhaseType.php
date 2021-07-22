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

use App\Entity\Project\GroupePhases;
use App\Entity\Project\Phase;
use App\Repository\Project\GroupePhasesRepository;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Phase $phase */
            $phase = $event->getData();
            $form = $event->getForm();

            $readOnly = $phase && null !== $phase->getId() && $phase->getProcesVerbal();
            $form
            ->add('position', HiddenType::class, ['disabled' => $readOnly, 'attr' => ['class' => 'position']])
            ->add('titre', TextType::class, ['disabled' => $readOnly, 'attr' => ['placeholder' => 'Titre phase']])
            ->add(
                'objectif',
                TextareaType::class,
                ['label' => 'Objectif', 'required' => false, 'disabled' => $readOnly, 'attr' => ['placeholder' => 'Objectif']]
            )
            ->add(
                'methodo',
                TextareaType::class,
                ['label' => 'Méthodologie', 'required' => false, 'disabled' => $readOnly, 'attr' => ['placeholder' => 'Méthodologie']]
            )
            ->add(
                'livrable',
                TextareaType::class,
                ['label' => 'Livrable', 'required' => false, 'disabled' => $readOnly, 'attr' => ['placeholder' => 'Livrable']]
            )
            ->add(
                'nbrJEH',
                NumberType::class,
                ['label' => 'Nombre de JEH', 'required' => false, 'disabled' => $readOnly, 'attr' => ['class' => 'nbrJEH']]
            )
            ->add(
                'prixJEH',
                NumberType::class,
                ['label' => 'Prix du JEH HT', 'required' => false, 'disabled' => $readOnly, 'attr' => ['class' => 'prixJEH']]
            )
            ->add(
                'dateDebut',
                DateType::class,
                [
                    'label' => 'Date de début',
                    'required' => false,
                    'disabled' => $readOnly,
                    'widget' => 'single_text']
            )
            ->add('delai', NumberType::class, ['disabled' => $readOnly, 'label' => 'Durée en nombre de jours', 'required' => false]);
        });

        if ($options['etude']) {
            $builder->add('groupe', Select2EntityType::class, [
                'class' => GroupePhases::class,
                'choice_label' => 'titre',
                'required' => false,
                'query_builder' => function (GroupePhasesRepository $er) use ($options) {
                    return $er->getGroupePhasesByEtude($options['etude']);
                },
                'label' => 'Groupe',
            ]);
        }

        if ($options['isAvenant']) {
            $builder->add(
                'etatSurAvenant',
                ChoiceType::class,
                ['choices' => array_flip(Phase::getEtatSurAvenantChoice()), 'required' => false]
            );
        }
    }

    public function getBlockPrefix()
    {
        return 'project_phasetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Phase::class,
            'isAvenant' => false,
            'etude' => null,
        ]);
    }
}
