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

use App\Controller\Publish\TraitementController;
use App\Entity\Personne\Membre;
use App\Entity\Project\Etude;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2ChoiceType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template', FileType::class, [
                'required' => true,
                'label' => 'dashboard.template',
                'translation_domain' => 'dashboard',
            ])
            ->add('name', Select2ChoiceType::class, [
                'required' => true,
                'label' => 'suivi.document_type',
                'translation_domain' => 'project',
                'choices' => [
                    'Gestion associative' => [
                        'Bulletin d\'adhésion' => TraitementController::DOCTYPE_BULLETIN_ADHESION,
                        'Convention Etudiant' => TraitementController::DOCTYPE_CONVENTION_ETUDIANT,
                        'Accord de confidentialité' => TraitementController::DOCTYPE_ACCORD_CONFIDENTIALITE,
                        'Déclaration étudiant étranger' => TraitementController::DOCTYPE_DECLARATION_ETUDIANT_ETR,
                    ],
                    'Suivi d\'étude' => [
                        'Devis' => TraitementController::DOCTYPE_DEVIS,
                        'suivi.avant_projet' => TraitementController::DOCTYPE_AVANT_PROJET,
                        'Convention Client' => TraitementController::DOCTYPE_CONVENTION_CLIENT,
                        'Convention Etude' => TraitementController::DOCTYPE_CONVENTION_ETUDE,
                        'Récapitulatif de mission' => TraitementController::DOCTYPE_RECAPITULATIF_MISSION,
                        'Cahier des Charges' => TraitementController::DOCTYPE_CAHIER_CHARGES,
                        'Proposition Commerciale' => TraitementController::DOCTYPE_PROPOSITION_COMMERCIALE,
                        'Convention Cadre Agile' => TraitementController::DOCTYPE_CONVENTION_CADRE_AGILE,
                        'Bon de Commande' => TraitementController::DOCTYPE_BON_COMMANDE,
                        'Questionnaire Satisfaction' => TraitementController::DOCTYPE_QUESTIONNAIRE_SATISFACTION,
                        'Descriptif de mission' => TraitementController::DOCTYPE_DESCRIPTIF_MISSION,
                        'Fiche de suivi d\'étude' => TraitementController::DOCTYPE_SUIVI_ETUDE,
                        'Avenant' => TraitementController::DOCTYPE_AVENANT,
                        'Procès verbal de recette intermédiaire' => TraitementController::DOCTYPE_PROCES_VERBAL_INTERMEDIAIRE,
                        'Procès verbal de recette final' => TraitementController::DOCTYPE_PROCES_VERBAL_FINAL,
                    ],
                    'Trésorerie' => [
                        'Facture d\'acompte' => TraitementController::DOCTYPE_FACTURE_ACOMTE,
                        'Facture intermédiaire' => TraitementController::DOCTYPE_FACTURE_INTERMEDIAIRE,
                        'Facture de solde' => TraitementController::DOCTYPE_FACTURE_SOLDE,
                        'Note de Frais' => TraitementController::DOCTYPE_NOTE_DE_FRAIS,
                        'Bulletin de Versement' => TraitementController::DOCTYPE_BULLETIN_DE_VERSEMENT,
                    ],
                ],
            ])
            ->add('etudiant', Select2EntityType::class, [
                'class' => Membre::class,
                'choice_label' => 'identifiant',
                'label' => 'Intervenant pour vérifier le template',
                'required' => false,
            ])
            ->add('etude', Select2EntityType::class, [
                'label' => 'Etude pour vérifier le template',
                'class' => Etude::class,
                'choice_label' => 'nom',
                'required' => false,
            ])
            ->add('verification', CheckboxType::class, [
                'label' => 'Activer la vérification',
                'required' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'suivi_doctypetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
