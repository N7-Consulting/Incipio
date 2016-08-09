<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use mgate\SuiviBundle\Entity\Phase;
use mgate\SuiviBundle\Entity\GroupePhasesRepository as GroupePhasesRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('position', 'hidden', array('attr' => array('class' => 'position')))
                ->add('titre', 'text',array('attr'=> array('placeholder' =>'Titre phase')))
                ->add('objectif', 'textarea', array('label' => 'Objectif', 'required' => false, 'attr'=> array('placeholder' =>'Objectif')))
                ->add('methodo', 'textarea', array('label' => 'Méthodologie', 'required' => false, 'attr'=> array('placeholder' =>'Méthodologie')))
                // Obsolète, la validation porte maintenant sur les groupes de phases
                // Une validation orale est impossible à prouver
                //->add('validation', 'choice', array('choices' => Phase::getValidationChoice(), 'required' => true))
                ->add('nbrJEH', 'integer', array('label' => 'Nombre de JEH', 'required' => false, 'attr' => array('class' => 'nbrJEH')))
                ->add('prixJEH', 'integer', array('label' => 'Prix du JEH HT', 'required' => false, 'attr' => array('class' => 'prixJEH')))
                ->add('dateDebut', 'genemu_jquerydate', array('label' => 'Date de début', 'format' => 'd/MM/y', 'required' => false, 'widget' => 'single_text'))
                ->add('delai', 'integer', array('label' => 'Durée en nombre de jours', 'required' => false));
        if ($options['etude']) {
            $builder->add('groupe', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\SuiviBundle\Entity\GroupePhases',
                'property' => 'titre',
                'required' => false,
                'query_builder' => function (GroupePhasesRepository $er) use ($options) {
                    return $er->getGroupePhasesByEtude($options['etude']);
                },
                'label' => 'Groupe',
                ));
        }

        if ($options['isAvenant']) {
            $builder->add('etatSurAvenant', 'choice', array('choices' => Phase::getEtatSurAvenantChoice(), 'required' => false));
        }
    }

    public function getName()
    {
        return 'mgate_suivibundle_phasetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Phase',
            'isAvenant' => false,
            'etude' => null,
        ));
    }
}
