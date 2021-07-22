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

use App\Entity\Project\Mission;
use App\Entity\Treso\BV;
use App\Repository\Project\MissionRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mandat', IntegerType::class)
            ->add('numero', IntegerType::class)
            ->add('nombreJEH', IntegerType::class)
            ->add('remunerationBruteParJEH', MoneyType::class)
            ->add(
                'dateDeVersement',
                DateType::class,
                ['label' => 'Date de versement', 'required' => true, 'widget' => 'single_text']
            )
            ->add(
                'dateDemission',
                DateType::class,
                ['label' => 'Date d\'émission', 'required' => true, 'widget' => 'single_text']
            )
            ->add('typeDeTravail', TextType::class)
            ->add('mission', Select2EntityType::class, [
                'label' => 'Mission',
                'class' => Mission::class,
                'choice_label' => 'reference',
                'required' => true,
                'query_builder' => function (MissionRepository $mr) {
                    return $mr->getMissionAndEtudeQueryBuilder();
                },
            ])
            ->add('numeroVirement', TextType::class, ['label' => 'Numéro de Virement', 'required' => true]);
    }

    public function getBlockPrefix()
    {
        return 'treso_bvtype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BV::class,
        ]);
    }
}
