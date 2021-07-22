<?php


namespace App\Form\Formation;

use App\Entity\Formation\Formation;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2ChoiceType;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PassationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', TextType::class, [
            'label' => 'formation.titre',
            'translation_domain' => 'formation',
            'required' => true, ])
            ->add('description', TextareaType::class, [
                'label' => 'formation.description',
                'translation_domain' => 'formation',
                'required' => true,
                'attr' => ['cols' => '100%', 'rows' => 5], ])
            ->add('categorie', Select2ChoiceType::class, [
                'label' => 'formation.categories',
                'translation_domain' => 'formation',
                'multiple' => false,
                'choices' => array_flip(Formation::getCategoriesChoice()),
                'required' => false, ]);
    }

    public function getBlockPrefix()
    {
        return 'suivi_formulairetype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Formation\Passation',
        ]);
    }
}
