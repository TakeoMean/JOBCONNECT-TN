<?php

namespace App\Controller\Admin;

use App\Entity\Candidate;
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
use Symfony\Component\HttpFoundation\RequestStack;

class SubscriptionCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public static function getEntityFqcn(): string
    {
        return Candidate::class; // We use Candidate as base but will override data
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Abonnement')
            ->setEntityLabelInPlural('Abonnements')
            ->setPageTitle('index', 'Gestion des Abonnements')
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
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c')
           ->from(Candidate::class, 'c')
           ->where('c.subscription != :free')
           ->setParameter('free', 'free')
           ->orderBy('c.subscriptionEndsAt', 'ASC');

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('fullName', 'Nom complet')
                ->setSortable(true),
            EmailField::new('email')
                ->setSortable(true),
            TextField::new('user_type', 'Type d\'utilisateur')
                ->setFormTypeOption('disabled', true)
                ->formatValue(function ($value, $entity) {
                    return 'Candidat'; // Since we're only showing candidates for now
                }),
            ChoiceField::new('subscription', 'Abonnement')
                ->setChoices([
                    'premium' => 'Premium',
                    'premium_plus' => 'Premium Plus',
                ])
                ->setRequired(true),
            DateTimeField::new('subscriptionEndsAt', 'Expiration')
                ->setSortable(true),
            TextField::new('phone', 'Téléphone'),
            TextField::new('city', 'Ville'),
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // The entityInstance should be a Candidate
        if ($entityInstance instanceof Candidate) {
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}