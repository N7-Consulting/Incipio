<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\TresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FactureType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('exercice', 'integer', array('label' => 'Exercice Comptable', 'required' => true))
                ->add('numero', 'integer', array('label' => 'Numéro de la Facture', 'required' => true))
                ->add('type', 'choice', array('choices' => \mgate\TresoBundle\Entity\Facture::getTypeChoices(), 'required' => true))
                ->add('objet', 'textarea',
                    array('label' => 'Objet de la Facture',
                        'required' => true,
                        'attr' => array(
                            'cols' => '100%',
                            'rows' => 5, ),
                        )
                    )
                ->add('details', 'collection', array(
                    'type' => new FactureDetailType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                ))
                ->add('beneficiaire', 'genemu_jqueryselect2_entity', array(
                    'class' => 'mgate\PersonneBundle\Entity\Prospect',
                    'property' => 'nom',
                    'required' => true,
                    'label' => 'Facture émise pour/par',
                ))
                ->add('montantADeduire', new FactureDetailType(), array('label' => 'Montant à déduire', 'required' => true))
                ->add('dateEmission', 'genemu_jquerydate', array('label' => 'Date d\'émission', 'required' => true, 'widget' => 'single_text'))
                ->add('dateVersement', 'genemu_jquerydate', array('label' => 'Date de versement', 'required' => false, 'widget' => 'single_text'));
    }

    public function getName()
    {
        return 'mgate_tresobundle_facturetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\Facture',
        ));
    }
}
