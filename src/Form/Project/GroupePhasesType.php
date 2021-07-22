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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupePhasesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('numero', HiddenType::class, ['attr' => ['class' => 'position']])
            ->add('titre', TextType::class, ['attr'=> ['placeholder' => 'Titre'], 'required' => true])
            ->add('description', TextareaType::class, ['attr' => ['placeholder' => 'Description']]);
    }

    public function getBlockPrefix()
    {
        return 'project_groupePhasestype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupePhases::class,
        ]);
    }
}
