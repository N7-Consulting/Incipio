<?php

namespace App\Form\Project;

use App\Entity\Project\Cca;
use App\Entity\Personne\Prospect;
use App\Form\Personne\ProspectType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CcaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'suivi.cca_form.nom_interne',
                'translation_domain' => 'project',
            ])
            ->add('knownProspect', CheckboxType::class, [
                'label' => 'suivi.etude_form.client_bdd',
                'translation_domain' => 'project',
                'required' => false,
            ])
            ->add('prospect', Select2EntityType::class, [
                'class' => Prospect::class,
                'choice_label' => 'nom',
                'label' => 'suivi.etude_form.prospect_existant',
                'translation_domain' => 'project',
                'required' => false,
            ])
            ->add('newProspect', ProspectType::class, [
                'label' => 'suivi.etude_form.prospect_nouveau',
                'translation_domain' => 'project',
                'required' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'project_ccatype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cca::class,
            'knownProspect' => false,
        ]);
    }
}
