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

use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\PersonneBundle\Form\MembreType as MembreType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends DocTypeType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('knownIntervenant', 'checkbox', array(
                'required' => false,
                'label' => "L'intervenant existe-t-il déjà dans la base de donnée ?",
                ))
            ->add('intervenant', 'genemu_jqueryselect2_entity', array(
               'class' => 'mgate\\PersonneBundle\\Entity\\Membre',
               'property' => 'personne.prenomNom',
               'label' => 'Intervenant',
               //'query_builder' => function(PersonneRepository $pr) { return $pr->getMembreOnly(); },
               'required' => true,
               ))
           ->add('newIntervenant', new MembreType(), array('label' => 'Nouvel intervenant ', 'required' => true))

            ->add('debutOm', 'genemu_jquerydate', array('label' => 'Début du Récapitulatif de Mission', 'required' => true, 'widget' => 'single_text'))
            ->add('finOm', 'genemu_jquerydate', array('label' => 'Fin du Récapitulatif de Mission', 'required' => true, 'widget' => 'single_text'))
            ->add('pourcentageJunior', 'percent', array('label' => 'Pourcentage junior', 'required' => true, 'precision' => 2))
            ->add('referentTechnique', 'genemu_jqueryselect2_entity', array(
               'class' => 'mgate\\PersonneBundle\\Entity\\Membre',
               'property' => 'personne.prenomNom',
               'label' => 'Référent Technique',
               'required' => false,
               ))
            ->add('repartitionsJEH', 'collection', array(
                'type' => new RepartitionJEHType(),
                'options' => array(
                    'data_class' => 'mgate\SuiviBundle\Entity\RepartitionJEH',
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                ));

            //->add('avancement','integer',array('label'=>'Avancement en %'))
            //->add('rapportDemande','checkbox', array('label'=>'Rapport pédagogique demandé','required'=>false))
            //->add('rapportRelu','checkbox', array('label'=>'Rapport pédagogique relu','required'=>false))
            //->add('remunere','checkbox', array('label'=>'Intervenant rémunéré','required'=>false));

        //->add('mission', new DocTypeType('mission'), array('label'=>' '));
        DocTypeType::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'mgate_suivibundle_mssiontype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Mission',
        ));
    }
}
