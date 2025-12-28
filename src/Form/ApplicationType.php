<?php

namespace App\Form;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // CV upload (not mapped, handled manually)
            ->add('cvFile', FileType::class, [
                'label' => 'Votre CV',
                'mapped' => false,
                'required' => true,
            ])

            // Motivation (mapped to Application entity)
            ->add('motivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'required' => true,
            ])

            // Phone (mapped)
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])

            // City (mapped)
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
