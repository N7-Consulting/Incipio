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

use App\Entity\Personne\Personne;
use App\Entity\Project\Ce;
use App\Repository\Personne\PersonneRepository;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubCeType extends DocTypeType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add(
            'contact',
            Select2EntityType::class,
            [
                'label' => "suivi.etude_form.contact_secondaire",
                'translation_domain' => 'project',
                'class' => Personne::class,
                'choice_label' => 'prenomNom',
                'query_builder' => function (PersonneRepository $pr) {
                    return $pr->getMembresByPoste('%vice-president%');
                },
                'required' => false,
            ]
        )
            ->add(
                'nbrDev',
                IntegerType::class,
                [
                    'label' => 'suivi.nombre_dev_estime',
                    'translation_domain' => 'project',
                    'required' => false,
                    'attr' => ['title' => 'Mettre 0 pour ne pas afficher la phrase indiquant le nombre d\'intervenant'],
                ]
            );
        DocTypeType::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'project_subcetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ce::class,
            'prospect' => '',
        ]);
    }
}
