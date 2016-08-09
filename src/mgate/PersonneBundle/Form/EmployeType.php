<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('personne', new PersonneType(), array('label' => ' ', 'signataire' => $options['signataire'], 'mini' => $options['mini']))
                ->add('poste');
    }

    public function getName()
    {
        return 'mgate_personnebundle_employetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\PersonneBundle\Entity\Employe',
            'mini' => false,
            'signataire' => false,
        ));
    }
}
