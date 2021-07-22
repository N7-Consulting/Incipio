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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuditType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                '' => 'Non défini',
                '0' => 'suivi.non_auditee',
                '1' => 'suivi.valide',
                '2' => 'suivi.valide_ss_doc',
                '3' => 'suivi.pb_mineur',
                '4' => 'suivi.pb_majeur',
            ],
            'translation_domain' => 'project',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'auditType';
    }
}
