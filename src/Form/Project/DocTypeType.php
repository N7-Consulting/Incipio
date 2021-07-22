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

use App\Entity\Personne\Personne;
use App\Entity\Project\Av;
use App\Entity\Project\AvMission;
use App\Entity\Project\DocType;
use App\Entity\Project\Mission;
use App\Form\Personne\EmployeType;
use App\Repository\Personne\PersonneRepository;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Version du document
        $builder->add(
            'version',
            IntegerType::class,
            [
                'label' => 'suivi.doc_type_form.version',
                'translation_domain' => 'project',
            ]);

        $builder->add(
            'signataire1',
            Select2EntityType::class,
            [
                'class' => Personne::class,
                'label' => 'suivi.doc_type_form.signataire_junior',
                'translation_domain' => 'project',
                'choice_label' => 'prenomNom',
                'query_builder' => function (PersonneRepository $pr) {
                    return $pr->getMembresByPoste('president%');
                },
                'required' => true,
            ]);

        // Si le document n'est ni un RM ni un AVRM
        if (Mission::class != $options['data_class'] &&
            AvMission::class != $options['data_class']
        ) {
            // le signataire 2 est un client/prospect
            $pro = $options['prospect'];
            if (Av::class != $options['data_class']) {
                $builder->add(
                    'knownSignataire2',
                    CheckboxType::class,
                    [
                        'label' => 'suivi.signataire_client_connu',
                        'translation_domain' => 'project',
                        'required' => false,
                    ]
                )
                    ->add(
                        'newSignataire2',
                        EmployeType::class,
                        [
                            'label' => 'suivi.nouveau_signataire_ajouter',
                            'translation_domain' => 'project',
                            'required' => false,
                            'signataire' => true,
                            'mini' => true,
                        ]
                    );
            }

            $builder->add(
                'signataire2',
                Select2EntityType::class,
                [
                    'class' => Personne::class,
                    'choice_label' => 'prenomNom',
                    'label' => 'suivi.signataire_client',
                    'translation_domain' => 'project',
                    'query_builder' => function (PersonneRepository $pr) use ($pro) {
                        return $pr->getEmployeOnly($pro);
                    },
                    'required' => false,
                ]);
        }

        $builder->add(
            'dateSignature',
            TypeDateType::class,
            [
                'label' => 'suivi.date_signature_document',
                'translation_domain' => 'project',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['autocomplete' => 'off'],
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'project_doctypetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocType::class,
            'prospect' => '',
        ]);
    }
}
