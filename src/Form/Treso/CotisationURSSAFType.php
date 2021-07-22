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

use App\Entity\Treso\CotisationURSSAF;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotisationURSSAFType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, ['label' => 'Libelle'])
            ->add(
                'dateDebut',
                DateType::class,
                ['label' => 'Applicable du', 'required' => true, 'widget' => 'single_text']
            )
            ->add(
                'dateFin',
                DateType::class,
                ['label' => 'Applicable au', 'required' => true, 'widget' => 'single_text']
            )
            ->add('tauxPartJE', PercentType::class, ['label' => 'Taux Part Junior', 'required' => false, 'scale' => 3])
            ->add('tauxPartEtu', PercentType::class, ['label' => 'Taux Part Etu', 'required' => false, 'scale' => 3])
            ->add(
                'surBaseURSSAF',
                CheckboxType::class,
                ['label' => 'Est indexé sur la base URSSAF ?', 'required' => false]
            )
            ->add('deductible', CheckboxType::class, ['label' => 'Est déductible ?', 'required' => false]);
    }

    public function getBlockPrefix()
    {
        return 'treso_cotisationurssaftype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CotisationURSSAF::class,
        ]);
    }
}
