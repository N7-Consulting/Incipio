<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Processus;

use App\Entity\Processus\Processus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use App\Entity\Personne\Personne;
use App\Repository\Personne\PersonneRepository;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


use App\Entity\Publish\Document;

class ProcessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['required' => true])
            ->add('pilote',
            Select2EntityType::class,
            [
                'class' => Personne::class,
                'label' => 'processus.pilote',
                'translation_domain' => 'processus',
                'choice_label' => 'prenomNom',
                'query_builder' => function (PersonneRepository $pr) {
                    return $pr->getMembreOnly();
                },
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'processus.description',
                'translation_domain' => 'processus',
                'required' => true,
                'attr' => ['cols' => '100%', 'rows' => 5], ]);
    }

    public function getBlockPrefix()
    {
        return 'processus_processtype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Processus::class,
        ]);
    }
}
