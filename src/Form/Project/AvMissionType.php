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

use App\Entity\Project\AvMission;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AvMissionType extends DocTypeType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('numero', IntegerType::class, [
            'label' => 'Numéro de l\'avenant',
            'required' => true,
        ])
            ->add('nouveauPourcentage', IntegerType::class, ['label' => 'Nouveau pourcentage'])
            
            ->add('differentielDelai', IntegerType::class, [
                'label' => 'Modification du Délai (+/- x jours)',
                'required' => true,
                ]);

        DocTypeType::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'project_avmissiontype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AvMission::class,
        ]);
    }
}
