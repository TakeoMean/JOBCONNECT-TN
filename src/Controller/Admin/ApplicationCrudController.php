<?php
// src/Controller/Admin/ApplicationCrudController.php
namespace App\Controller\Admin;

use App\Entity\Application;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ApplicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Application::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('candidate', 'Candidat'),
            AssociationField::new('jobOffer', 'Offre d\'emploi'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'pending' => 'En attente',
                    'accepted' => 'Accepté',
                    'rejected' => 'Rejeté',
                ]),
            TextareaField::new('coverLetter', 'Lettre de motivation'),
            DateTimeField::new('appliedAt', 'Date de candidature')->hideOnForm(),
        ];
    }
}