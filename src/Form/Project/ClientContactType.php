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
use App\Entity\Project\ClientContact;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'faitPar',
                Select2EntityType::class,
                [
                    'label' => 'Fait par',
                    'class' => Personne::class,
                    'choice_label' => 'prenomNom',
                    'required' => true,
                ]
            )
            ->add(
                'date',
                TypeDateType::class,
                ['label' => 'Date du contact', 'required' => true, 'widget' => 'single_text']
            )
            ->add('objet', TextType::class, ['label' => 'Objet'])
            ->add(
                'contenu',
                TextareaType::class,
                ['label' => 'Résumé du contact', 'attr' => ['cols' => '100%', 'rows' => 5]]
            )
            ->add('moyenContact', MoyenContactType::class, ['label' => 'Contact effectué par']);
    }

    public function getBlockPrefix()
    {
        return 'project_clientcontacttype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClientContact::class,
        ]);
    }
}
