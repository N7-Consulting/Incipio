<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\TresoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', 'text',
                    array('label' => 'Libellé du compte',
                        'required' => true, )
                    )
                ->add('numero', 'text', array('label' => 'Numéro de compte', 'required' => true, 'attr' => array('maxlength' => 6)))
                ->add('categorie', 'checkbox', array('label' => 'Est utilisé comme catégorie ? ', 'required' => false));
    }

    public function getName()
    {
        return 'mgate_tresobundle_comptetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\Compte',
        ));
    }
}
