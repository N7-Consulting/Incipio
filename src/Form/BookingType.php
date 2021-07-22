<?php

namespace App\Form;

use App\Entity\Booking\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('commentaires', TextareaType::class, [
                'label' => 'dashboard.ev_commentaires',
                'translation_domain' => 'dashboard',
                'required' => false,
                'attr' => ['cols' => '100%', 'rows' => 5], ])
            ->add('dateDebut')
            ->add('dateFin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
