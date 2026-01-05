<?php
// src/Controller/Admin/JobOfferCrudController.php
namespace App\Controller\Admin;

use App\Entity\JobOffer;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class JobOfferCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JobOffer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextareaField::new('description', 'Description'),
            TextField::new('location', 'Localisation'),
            NumberField::new('salary', 'Salaire'),
            BooleanField::new('isRemote', 'Télétravail'),
            ChoiceField::new('contractType', 'Type de contrat')
                ->setChoices([
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Stage' => 'Stage',
                    'Freelance' => 'Freelance',
                ]),
            BooleanField::new('isPublished', 'Publié'),
            AssociationField::new('recruiter', 'Recruteur'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
        ];
    }
}