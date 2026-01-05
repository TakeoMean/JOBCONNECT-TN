<?php
// src/Controller/Admin/CandidateCrudController.php
namespace App\Controller\Admin;

use App\Entity\Candidate;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class CandidateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Candidate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Candidats');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('fullName', 'Nom complet'),
            TextField::new('phone', 'Téléphone'),
            TextField::new('city', 'Ville'),
            ChoiceField::new('subscription', 'Abonnement')
                ->setChoices([
                    'free' => 'Gratuit',
                    'premium' => 'Premium',
                    'premium_plus' => 'Premium Plus',
                ]),
            DateTimeField::new('subscriptionEndsAt', 'Fin d\'abonnement'),
            BooleanField::new('isVerified', 'Vérifié'),
            BooleanField::new('isApproved', 'Approuvé'),
            ImageField::new('photo', 'Photo')
                ->setBasePath('/uploads/candidate/photo')
                ->setUploadDir('public/uploads/candidate/photo')
                ->hideOnIndex(),
            TextField::new('cvPath', 'CV')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }
}