<?php

namespace App\Form\Project;

use App\Entity\Project\Cca;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubCcaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        DocTypeType::buildForm($builder, $options);
        $builder->add('dateFin', DateType::class, [
                'label' => 'suivi.date_fin',
                'translation_domain' => 'project',
                'widget' => 'single_text',
                'required' => true,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'project_ccasubtype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cca::class,
            'prospect' => '',
        ]);
    }
}
