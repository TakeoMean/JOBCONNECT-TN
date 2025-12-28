<?php

namespace App\Form;

use App\Entity\JobOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobOfferType extends AbstractType
{
    // Build the form for creating a job offer
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Title of the job offer
            ->add('title', TextType::class, [
                'label' => 'Titre du poste',
                'required' => true,
            ])

            // Description of the job offer
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
            ])

            // Location where the job is based
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'required' => true,
            ])

            // Salary for the job (optional)
            ->add('salary', IntegerType::class, [
                'label' => 'Salaire (optionnel)',
                'required' => false, // Optional field
                'attr' => ['placeholder' => 'e.g. 35000'], // Placeholder for clarity
            ])

            // Contract type for the job
            ->add('contractType', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Stage' => 'Stage',
                    'Freelance' => 'Freelance',
                ],
                'required' => true, // Contract type is required
            ])

            // Option for remote work
            ->add('isRemote', CheckboxType::class, [
                'label' => 'Télétravail ?',
                'required' => false, // Not required, so user can leave it unchecked
                'value' => '1', // Optional: can be set to 1 if checked
                'attr' => [
                    'class' => 'custom-checkbox', // Add custom CSS class if needed
                ],
            ]);
    }

    // Configure the options for the form
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobOffer::class, // The form is tied to the JobOffer entity
        ]);
    }
}
