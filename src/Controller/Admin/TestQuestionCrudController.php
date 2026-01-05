<?php
// src/Controller/Admin/TestQuestionCrudController.php
namespace App\Controller\Admin;

use App\Entity\TestQuestion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class TestQuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TestQuestion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('test', 'Test'),
            TextField::new('question', 'Question'),
            ChoiceField::new('type', 'Type')
                ->setChoices([
                    'multiple_choice' => 'Choix multiple',
                    'text' => 'Texte libre',
                    'true_false' => 'Vrai/Faux',
                ]),
            TextareaField::new('options', 'Options (JSON)'),
            TextField::new('correctAnswer', 'Réponse correcte'),
        ];
    }
}