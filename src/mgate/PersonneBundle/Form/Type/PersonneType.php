<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\PersonneBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use mgate\PersonneBundle\Form\Type\SexeType;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['user']) {
            $helpEmail = array('title' => "Pas d'adresse etu. Cette adresse est reprise dans les AP des études suivies.");
            $helpMobile = array('title' => 'Sous la forme: 06 78 39 .. Ce téléphone est repris dans les AP des études suivies.');
        } else {
            $helpEmail = array();
            $helpMobile = array();
        }

        $builder
                ->add('prenom')
                ->add('nom')
                ->add('sexe', new SexeType())
                ->add('mobile', 'text', array('required' => false, 'attr' => $helpMobile))
                ->add('email', 'email', array('required' => false, 'attr' => $helpEmail))
                ->add('estAbonneNewsletter', 'checkbox', array('label' => 'Abonné Newsletter ?', 'required' => false))
                ->add('emailEstValide', 'checkbox', array('label' => 'Email Valide ?', 'required' => false));

        if (!$options['mini'] && !$options['user']) {
            $builder->add('fix', 'text', array('required' => false));
        }
        if (!$options['mini']) {
            $builder->add('adresse', 'textarea', array('label' => 'Adresse','required' => false))
                ->add('codepostal', 'text', array('label' => 'Code Postal','required' => false))
                ->add('ville', 'text', array('label' => 'Ville','required' => false))
                ->add('pays', 'text', array('label' => 'pays','required' => false));
        }
    }

    public function getName()
    {
        return 'mgate_personnebundle_personnetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\PersonneBundle\Entity\Personne',
            'mini' => false,
            'user' => false,
            'signataire' => false,
        ));
    }
}
