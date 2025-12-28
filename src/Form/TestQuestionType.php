<?php

namespace App\Form;

use App\Entity\TestQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', TextType::class, [
                'label' => false, // We'll handle the label in the template
                'attr' => [
                    'class' => 'form-control form-control-lg border-0 shadow-sm',
                    'placeholder' => 'Entrez votre question ici'
                ]
            ])
            ->add('answers', CollectionType::class, [
                'entry_type' => TestAnswerType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__answer__',
                'label' => false,
                'attr' => ['class' => 'vstack gap-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TestQuestion::class,
        ]);
    }
}