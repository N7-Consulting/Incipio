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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', 'textarea',
                    array('label' => 'Description de la dépense',
                        'required' => false,
                        'attr' => array(
                            'cols' => '100%',
                            'rows' => 2, ),
                        )
                    )
                ->add('montantHT', 'money', array('label' => 'Prix H.T.', 'required' => false))
                ->add('tauxTVA', 'number', array('label' => 'Taux TVA (%)', 'required' => false))
                ->add('compte', 'genemu_jqueryselect2_entity', array(
                        'class' => 'mgate\TresoBundle\Entity\Compte',
                        'property' => 'libelle',
                        'required' => false,
                        'label' => 'Catégorie',
                        'configs' => array('placeholder' => 'Sélectionnez une catégorie', 'allowClear' => true),
                        ));
    }

    public function getName()
    {
        return 'mgate_tresobundle_facturedetailtype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\FactureDetail',
        ));
    }
}
