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

class BVType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mandat', 'integer')
            ->add('numero', 'integer')
            ->add('nombreJEH', 'integer')
            ->add('remunerationBruteParJEH', 'money')
            ->add('dateDeVersement', 'genemu_jquerydate', array('label' => 'Date de versement', 'required' => true, 'widget' => 'single_text'))
            ->add('dateDemission', 'genemu_jquerydate', array('label' => 'Date d\'émission', 'required' => true, 'widget' => 'single_text'))
            ->add('typeDeTravail', 'text')
            ->add('mission', 'genemu_jqueryselect2_entity', array(
                      'label' => 'Mission',
                       'class' => 'mgate\\SuiviBundle\\Entity\\Mission',
                       'property' => 'reference',
                       'required' => true, ))
            ->add('numeroVirement', 'text', array('label' => 'Numéro de Virement', 'required' => true));
    }

    public function getName()
    {
        return 'mgate_tresobundle_bvtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\BV',
        ));
    }
}
