<?php

namespace App\Form;

use App\Entity\Test;
use App\Form\TestQuestionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du test',
                'attr' => [
                    'class' => 'form-control form-control-lg border-0 shadow-sm',
                    'placeholder' => 'Entrez le titre du test'
                ],
                'label_attr' => ['class' => 'form-label fw-semibold']
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (minutes)',
                'attr' => [
                    'class' => 'form-control form-control-lg border-0 shadow-sm',
                    'placeholder' => 'Durée en minutes',
                    'min' => 1
                ],
                'label_attr' => ['class' => 'form-label fw-semibold']
            ])
            ->add('minScore', IntegerType::class, [
                'label' => 'Score minimum (%)',
                'attr' => [
                    'class' => 'form-control form-control-lg border-0 shadow-sm',
                    'placeholder' => 'Score minimum requis',
                    'min' => 0,
                    'max' => 100
                ],
                'label_attr' => ['class' => 'form-label fw-semibold']
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => TestQuestionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
                'attr' => ['class' => 'vstack gap-4']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    }
}