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

use App\Entity\Project\RepartitionJEH;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepartitionJEHType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nbrJEH', IntegerType::class, ['required' => true, 'label' => 'Nombre JEH'])
            ->add('prixJEH', IntegerType::class, ['required' => true, 'label' => 'Prix JEH', 'attr' => ['min' => 80]]);
    }

    public function getBlockPrefix()
    {
        return 'project_RepartitionJEHType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RepartitionJEH::class,
            'type' => '',
            'prospect' => '',
            'phases' => '',
        ]);
    }
}
