<?php

namespace App\Form\Personne;

use App\Entity\Personne\SecteurActivite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecteurActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intitule', TextType::class, ['required' => true])
            ->add('description', TextType::class, ['required' => false]);
    }

    public function getBlockPrefix()
    {
        return 'secteur_filieretype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SecteurActivite::class,
        ]);
    }
}
