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

use App\Entity\Project\Etude;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuditEtudeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'auditDate',
                DateType::class,
                [
                    'label' => 'Date de l\'audit',
                    'widget' => 'single_text',
                    'required' => false,
                ])
            ->add(
                'auditType',
                AuditType::class,
                ['label' => 'Etat de l\'audit',
                'required' => true,
                'choices' => array_flip(Etude::getAuditTypeChoice()),
                ]
            )
            ->add('auditCommentaires', TextareaType::class, [
                'label' => 'Commentaires',
                'translation_domain' => 'formation',
                'required' => false,
                'attr' => ['cols' => '100%', 'rows' => 5], ]);
    }

    public function getBlockPrefix()
    {
        return 'audit_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etude::class,
        ]);
    }
}
