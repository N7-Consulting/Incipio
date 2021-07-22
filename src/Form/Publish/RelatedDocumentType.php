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

use App\Entity\Formation\Formation;
use App\Entity\Personne\Membre;
use App\Entity\Personne\Prospect;
use App\Entity\Processus\Processus;
use App\Entity\Project\Etude;
use App\Entity\Publish\RelatedDocument;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelatedDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['etude']) {
            $builder->add('etude', Select2EntityType::class, [
                'class' => Etude::class,
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Document lié à l\'étude',
                'data' => $options['etude'],
                'attr' => ['style' => 'min-width: 300px'],
                'configs' => ['placeholder' => 'Sélectionnez une étude', 'allowClear' => true],
            ]);
        }
        if ($options['prospect']) {
            $builder->add('prospect', Select2EntityType::class, [
                'class' => Prospect::class,
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Document lié au prospect',
                'attr' => ['style' => 'min-width: 300px'],
                'configs' => ['placeholder' => 'Sélectionnez un prospect', 'allowClear' => true],
            ]);
        }
        if ($options['formation']) {
            $builder->add('formation', Select2EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'titre',
                'required' => false,
                'label' => 'Document lié à la formation',
                'attr' => ['style' => 'min-width: 300px'],
                'configs' => ['placeholder' => 'Sélectionnez une formation', 'allowClear' => true],
            ]);
        }
        if ($options['etudiant'] || $options['etude']) {
            $builder->add('membre', Select2EntityType::class, [
                'label' => 'Document lié à l\'étudiant (optionnel)',
                'class' => Membre::class,
                'choice_label' => 'personne.prenomNom',
                'required' => false,
                'attr' => ['style' => 'min-width: 300px'],
                'configs' => ['placeholder' => 'Sélectionnez un étudiant', 'allowClear' => true], ]);
        }
        if ($options['processus']) {
            $builder->add('processus', Select2EntityType::class, [
                'class' => Processus::class,
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Fiche de processus',
                'attr' => ['style' => 'min-width: 300px'],
                'configs' => ['placeholder' => 'Sélectionnez un fichier', 'allowClear' => true],
            ]);
        }
    }

    public function getBlockPrefix()
    {
        return 'suivi_categoriedocumenttype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RelatedDocument::class,
            'etude' => null,
            'etudiant' => null,
            'prospect' => null,
            'processus' => null,
            'formation' => null,
        ]);
    }
}
