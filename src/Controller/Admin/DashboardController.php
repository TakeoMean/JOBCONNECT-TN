<?php
// src/Controller/Admin/DashboardController.php
namespace App\Controller\Admin;

use App\Entity\Candidate;
use App\Entity\Recruiter;
use App\Entity\JobOffer;
use App\Entity\Application;
use App\Entity\Test;
use App\Entity\TestQuestion;
use App\Entity\TestAnswer;
use App\Entity\Notification;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // Check if user has admin role
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('joboffer_list');
        }

        // Redirect to candidates list by default (standard EasyAdmin behavior)
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(CandidateCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JobConnect TN - Admin')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Candidats', 'fas fa-user-graduate', Candidate::class);
        yield MenuItem::linkToCrud('Recruteurs', 'fas fa-building', Recruiter::class);
        
        yield MenuItem::section('Offres & Candidatures');
        yield MenuItem::linkToCrud('Offres d\'emploi', 'fas fa-briefcase', JobOffer::class);
        yield MenuItem::linkToCrud('Candidatures', 'fas fa-file-alt', Application::class);
        
        yield MenuItem::section('Tests');
        yield MenuItem::linkToCrud('Tests', 'fas fa-clipboard-check', Test::class);
        yield MenuItem::linkToCrud('Questions', 'fas fa-question', TestQuestion::class);
        yield MenuItem::linkToCrud('Réponses', 'fas fa-check', TestAnswer::class);
        
        yield MenuItem::section('Notifications');
        yield MenuItem::linkToCrud('Notifications', 'fas fa-bell', Notification::class);
        
        yield MenuItem::section('');
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-external-link-alt', 'joboffer_list');
    }
}
