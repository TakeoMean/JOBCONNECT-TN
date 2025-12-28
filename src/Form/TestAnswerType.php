<?php

namespace App\Form;

use App\Entity\TestAnswer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('answer', TextType::class, [
                'label' => false, // We'll handle the label in the template
                'attr' => [
                    'class' => 'form-control border-0 shadow-sm',
                    'placeholder' => 'Entrez une option de réponse'
                ]
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label' => false, // We'll handle the checkbox manually in template
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TestAnswer::class,
        ]);
    }
}
