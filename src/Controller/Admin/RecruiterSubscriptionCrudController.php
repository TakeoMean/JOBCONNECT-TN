<?php

namespace App\Controller\Admin;

use App\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RecruiterSubscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recruiter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Abonnement Recruteur')
            ->setEntityLabelInPlural('Abonnements Recruteurs')
            ->setPageTitle('index', 'Gestion des Abonnements Recruteurs')
            ->setPageTitle('edit', 'Modifier l\'abonnement')
            ->setPageTitle('detail', 'Détails de l\'abonnement')
            ->setDefaultSort(['subscriptionEndsAt' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->setLabel('Modifier');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel('Supprimer');
            });
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('subscription')
            ->add('subscriptionEndsAt');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->container->get('doctrine')->getManager()->createQueryBuilder();
        $qb->select('r')
           ->from(Recruiter::class, 'r')
           ->where('r.subscription != :free')
           ->setParameter('free', 'free')
           ->orderBy('r.subscriptionEndsAt', 'ASC');

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('companyName', 'Entreprise')
                ->setSortable(true),
            EmailField::new('email')
                ->setSortable(true),
            TextField::new('user_type', 'Type d\'utilisateur')
                ->setFormTypeOption('disabled', true)
                ->formatValue(function ($value, $entity) {
                    return 'Recruteur';
                }),
            ChoiceField::new('subscription', 'Abonnement')
                ->setChoices([
                    'pro' => 'Pro',
                    'enterprise' => 'Enterprise',
                ])
                ->setRequired(true),
            DateTimeField::new('subscriptionEndsAt', 'Expiration')
                ->setSortable(true),
            TextField::new('phone', 'Téléphone'),
            TextField::new('city', 'Ville'),
        ];
    }
}