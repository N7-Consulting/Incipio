<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Publish;

use App\Entity\Publish\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Nom du fichier (optionnel)', 'required' => false])
                ->add('file', FileType::class, ['label' => 'Fichier', 'required' => true, 'attr' => ['cols' => '100%', 'rows' => 5]]);
        if ($options['etude'] || $options['etudiant'] || $options['prospect']) {
            $builder->add('relation', RelatedDocumentType::class, [
                'label' => '',
                'etude' => $options['etude'],
                'etudiant' => $options['etudiant'],
                'prospect' => $options['prospect'],
                ]);
        }
    }

    public function getBlockPrefix()
    {
        return 'suivi_documenttype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'etude' => null,
            'etudiant' => null,
            'passation' => null,
            'prospect' => null,
            'processus' => null,
            'formation' => null,
        ]);
    }
}
