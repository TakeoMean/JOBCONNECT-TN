<?php
// src/Controller/Admin/TestAnswerCrudController.php
namespace App\Controller\Admin;

use App\Entity\TestAnswer;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class TestAnswerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TestAnswer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('candidate', 'Candidat'),
            AssociationField::new('test', 'Test'),
            TextareaField::new('answers', 'Réponses (JSON)'),
            NumberField::new('score', 'Score'),
            DateTimeField::new('submittedAt', 'Soumis le')->hideOnForm(),
        ];
    }
}