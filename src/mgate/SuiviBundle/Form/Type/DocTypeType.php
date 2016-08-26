<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\SuiviBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use mgate\PersonneBundle\Entity\PersonneRepository;
use mgate\PersonneBundle\Form\Type\EmployeType;
use mgate\PersonneBundle\Entity\Prospect;

class DocTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Version du document
        $builder->add('version', 'integer', array('label' => 'Version du document'));

        $builder->add('signataire1', 'genemu_jqueryselect2_entity',
            array('label' => 'Signataire Junior',
                   'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                   'property' => 'prenomNom',
                   'query_builder' => function (PersonneRepository $pr) { return $pr->getMembresByPoste('president%'); },
                   'required' => true, ));

        // Si le document n'est ni une FactureVente ni un RM
        if ($options['data_class'] != 'mgate\SuiviBundle\Entity\Mission') {
            // le signataire 2 est l'intervenant

            $pro = $options['prospect'];
            if ($options['data_class'] != 'mgate\SuiviBundle\Entity\Av') {
                $builder->add('knownSignataire2', 'checkbox', array(
                    'required' => false,
                    'label' => 'Le signataire client existe-t-il déjà dans la base de donnée ?',
                    ))
                ->add('newSignataire2', new EmployeType(), array('label' => 'Nouveau signataire '.$pro->getNom(), 'required' => false, 'signataire' => true, 'mini' => true));
            }
            $builder->add('signataire2', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                'property' => 'prenomNom',
                'label' => 'Signataire '.$pro->getNom(),
                'query_builder' => function (PersonneRepository $pr) use ($pro) { return $pr->getEmployeOnly($pro); },
                'required' => false,
                ));
        }

        $builder->add('dateSignature', 'genemu_jquerydate', array('label' => 'Date de Signature du document', 'required' => false, 'format' => 'dd/MM/yyyy', 'widget' => 'single_text'));
    }

    public function getName()
    {
        return 'mgate_suivibundle_doctypetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\DocType',
            'prospect' => '',
        ));
    }
}
