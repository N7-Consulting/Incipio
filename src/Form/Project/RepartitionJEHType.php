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

use App\Entity\Project\RepartitionJEH;
use App\Entity\Project\Phase;
use App\Repository\Project\PhaseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepartitionJEHType extends AbstractType
{
    protected $etude;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['etude'])) {
            throw new \LogicException('A RepartitionJEHType can\'t be build without associated Etude object.');
        }

        $this->etude = $options['etude'];

        $builder
            ->add(
                'phase',
                EntityType::class, [
                'class' => Phase::class,
                'choice_label' => 'titre',
                'query_builder' => function (PhaseRepository $pr) {
                    return $pr->getByEtudeQuery($this->etude);
                },
                'required' => true,
                'label' => 'Phase']
                )
            ->add('nbrJEH', NumberType::class, ['required' => true, 'label' => 'Nombre JEH'])
            ->add('prixJEH', NumberType::class, ['required' => true, 'label' => 'Prix JEH', 'attr' => ['min' => 80]]);
    }

    public function getBlockPrefix()
    {
        return 'project_RepartitionJEHType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['etude']);
        $resolver->setDefaults([
            'data_class' => RepartitionJEH::class,
            'type' => '',
            'prospect' => '',
            'phases' => '',
            'etude' => '',
        ]);
    }
}
