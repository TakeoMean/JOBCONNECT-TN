<?php
// src/Controller/Admin/DashboardController.php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Candidate;
use App\Entity\Recruiter;
use App\Entity\JobOffer;
use App\Entity\Application;
use App\Entity\Test;
use App\Entity\TestQuestion;
use App\Entity\TestAnswer;
use App\Entity\Notification;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JobConnect TN - Admin')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Candidats', 'fas fa-user-graduate', Candidate::class);
        yield MenuItem::linkToCrud('Recruteurs', 'fas fa-building', Recruiter::class);
        yield MenuItem::linkToCrud('Offres', 'fas fa-briefcase', JobOffer::class);
        yield MenuItem::linkToCrud('Candidatures', 'fas fa-file-alt', Application::class);
        yield MenuItem::linkToCrud('Tests', 'fas fa-clipboard-check', Test::class);
        yield MenuItem::linkToCrud('Questions', 'fas fa-question', TestQuestion::class);
        yield MenuItem::linkToCrud('Réponses', 'fas fa-check', TestAnswer::class);
        yield MenuItem::linkToCrud('Notifications', 'fas fa-bell', Notification::class);
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-external-link-alt', 'joboffer_list');
    }
}