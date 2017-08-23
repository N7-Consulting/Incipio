<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\SuiviBundle\Form\Type;

use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Mgate\PersonneBundle\Entity\PersonneRepository;
use Mgate\PersonneBundle\Form\Type\ProspectType;
use Mgate\SuiviBundle\Entity\Etude;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('knownProspect', CheckboxType::class, [
                'required' => false,
                'label' => 'Le signataire client existe-t-il déjà dans la base de donnée ?',
                ])
             ->add('prospect', Select2EntityType::class, [
                'class' => 'Mgate\PersonneBundle\Entity\Prospect',
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Prospect existant',
                ])
            ->add('newProspect', ProspectType::class, ['label' => 'Nouveau prospect:', 'required' => false])
            ->add('nom', TextType::class, ['label' => 'Nom interne de l\'étude'])
            ->add('description', TextareaType::class, ['label' => 'Présentation interne de l\'étude', 'required' => false, 'attr' => ['cols' => '100%', 'rows' => 5]])
            ->add('mandat', IntegerType::class)
            ->add('num', IntegerType::class, ['label' => 'Numéro de l\'étude', 'required' => false])
            ->add('confidentiel', CheckboxType::class, ['label' => 'Confidentialité :', 'required' => false, 'attr' => ['title' => "Si l'étude est confidentielle, elle ne sera visible que par vous et les membres du CA."]])
            ->add('suiveur', Select2EntityType::class,
                ['label' => 'Suiveur de projet',
                       'class' => 'Mgate\\PersonneBundle\\Entity\\Personne',
                       'choice_label' => 'prenomNom',
                       'query_builder' => function (PersonneRepository $pr) {
                           return $pr->getMembreOnly();
                       },
                       'required' => false, ])
            ->add('suiveurQualite', Select2EntityType::class,
                ['label' => 'Suiveur qualité',
                       'class' => 'Mgate\\PersonneBundle\\Entity\\Personne',
                       'choice_label' => 'prenomNom',
                       'query_builder' => function (PersonneRepository $pr) {
                           return $pr->getMembreOnly();
                       },
                       'required' => false, ])
            ->add('domaineCompetence', Select2EntityType::class, [
                'class' => 'Mgate\SuiviBundle\Entity\DomaineCompetence',
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Domaine de compétence',
                ])
            ->add('sourceDeProspection', ChoiceType::class, [
                'choices' => array_flip(Etude::getSourceDeProspectionChoice()),
                'required' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'Mgate_suivibundle_etudetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Mgate\SuiviBundle\Entity\Etude',
        ]);
    }
}
