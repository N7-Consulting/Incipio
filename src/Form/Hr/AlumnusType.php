<?php

namespace App\Form\Hr;

use App\Entity\Hr\Alumnus;
use App\Entity\Personne\Personne;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Project\MoyenContactType;

class AlumnusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commentaire', TextType::class, ['label' => 'Objet'])
            ->add(
                'personne',
                Select2EntityType::class,
                [
                    'label' => 'Alumnus contacté',
                    'class' => Personne::class,
                    'choice_label' => 'prenomNom',
                    'required' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Alumnus::class,
        ]);
    }
}
