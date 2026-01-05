<?php
// src/Controller/Admin/RecruiterCrudController.php
namespace App\Controller\Admin;

use App\Entity\Recruiter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class RecruiterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recruiter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Recruteurs');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('companyName', 'Nom de l\'entreprise'),
            TextField::new('responsiblePerson', 'Personne responsable'),
            TextField::new('phone', 'Téléphone'),
            TextField::new('city', 'Ville'),
            TextField::new('sector', 'Secteur'),
            TextareaField::new('address', 'Adresse'),
            ChoiceField::new('subscription', 'Abonnement')
                ->setChoices([
                    'free' => 'Gratuit',
                    'pro' => 'Pro',
                    'enterprise' => 'Enterprise',
                ]),
            DateTimeField::new('subscriptionEndsAt', 'Fin d\'abonnement'),
            BooleanField::new('isVerified', 'Vérifié'),
            BooleanField::new('isApproved', 'Approuvé'),
            ImageField::new('logo', 'Logo')
                ->setBasePath('/uploads/logos')
                ->setUploadDir('public/uploads/logos')
                ->hideOnIndex(),
            ImageField::new('photo', 'Photo')
                ->setBasePath('/uploads/recruiters')
                ->setUploadDir('public/uploads/recruiters')
                ->hideOnIndex(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }
}