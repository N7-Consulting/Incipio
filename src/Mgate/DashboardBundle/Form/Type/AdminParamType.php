<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 29/01/2017
 * Time: 10:33.
 */

namespace Mgate\DashboardBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Mgate\DashboardBundle\Entity\AdminParam;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminParamType extends AbstractType
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = $this->em->getRepository('MgateDashboardBundle:AdminParam')->findAll([], ['priority' => 'desc']);

        foreach ($fields as $field) {
            /* @var $field AdminParam */
            $builder->add($field->getName(), $this->chooseType($field->getParamType()),
                ['required' => $field->getRequired(),
                    'label' => $field->getParamLabel(),
                    'attr' => ['tooltip' => $field->getParamDescription()],
                ]);
        }
    }

    public function getBlockPrefix()
    {
        return 'Mgate_dashboardbundle_adminparam';
    }

    /**
     * Returns the class associated with form type string.
     *
     * @param $formType string the string representing the form type
     *
     * @return mixed
     */
    private function chooseType($formType)
    {
        if ($formType === 'string') {
            return TextType::class;
        } elseif ($formType === 'integer') {
            return IntegerType::class;
        } elseif ($formType === 'number') {
            return NumberType::class;
        } elseif ($formType === 'url') {
            return UrlType::class;
        } else {
            throw new \LogicException('Type ' . $formType . ' is invalid.');
        }
    }
}
