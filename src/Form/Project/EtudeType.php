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
use App\Entity\Personne\Prospect;
use App\Entity\Project\DomaineCompetence;
use App\Entity\Project\Etude;
use App\Entity\Project\Cca;
use App\Form\Personne\ProspectType;
use App\Repository\Personne\PersonneRepository;
use App\Repository\Project\CcaRepository;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2ChoiceType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('boutonCcaActive', CheckboxType::class, [
                'label' => 'suivi.etude_form.cca_active',
                'translation_domain' =>  'project',
                'attr' => ['title' => 'suivi.etude_form.cca_active_tooltip'],
                'required' => false,
                'property_path' => 'ccaActive',
            ])
            ->add('cca', Select2EntityType::class, [
                'class' => Cca::class,
                'label' => 'suivi.convention_cadre_agile',
                'translation_domain' => 'project',
                'query_builder' => function (CcaRepository $cr) {
                    return $cr->findNotFinished();
                },
                'required' => false,
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
            ])
            ->add('nom', TextType::class, [
                'label' => 'suivi.etude_form.nom_interne',
                'translation_domain' => 'project',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'suivi.etude_form.presentation_interne',
                'translation_domain' => 'project',
                'attr' => ['cols' => '100%', 'rows' => 5],
                'required' => false,
            ])
            ->add('mandat', IntegerType::class, [
                'label' => 'suivi.etude_form.mandat',
                'translation_domain' => 'project',
            ])
            ->add('num', IntegerType::class, [
                'label' => 'suivi.etude_form.numero',
                'translation_domain' => 'project',
                'required' => false,
            ])
            ->add('confidentiel', CheckboxType::class, [
                'label' => 'suivi.etude_form.confidentialite',
                'translation_domain' => 'project',
                'attr' => ['title' => 'suivi.etude_form.confidentialite_tooltip'],
                'required' => false,
            ])
            ->add('suiveur', Select2EntityType::class, [
                'label' => 'suivi.etude_form.suiveur_projet',
                'translation_domain' => 'project',
                'class' => Personne::class,
                'choice_label' => 'prenomNom',
                'query_builder' => function (PersonneRepository $pr) {
                    return $pr->getMembreOnly();
                },
                'required' => false,
            ])
            ->add('suiveurQualite', Select2EntityType::class, [
                'label' => 'suivi.etude_form.suiveur_qualite',
                'translation_domain' => 'project',
                'class' => Personne::class,
                'choice_label' => 'prenomNom',
                'query_builder' => function (PersonneRepository $pr) {
                    return $pr->getMembreOnly();
                },
                'required' => false,
            ])
            ->add('domaineCompetence', Select2EntityType::class, [
                'class' => DomaineCompetence::class,
                'choice_label' => 'nom',
                'label' => 'suivi.etude_form.domaine_competence',
                'translation_domain' => 'project',
                'required' => false,
            ])
            ->add('sourceDeProspection', Select2ChoiceType::class, [
                'choices' => array_flip(Etude::getSourceDeProspectionChoice()),
                'label' => 'suivi.etude_form.source_prospection',
                'translation_domain' => 'project',
                'required' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'project_etudetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etude::class,
        ]);
    }
}
