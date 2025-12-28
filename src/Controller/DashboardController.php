<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        // Check if the user is a candidate
        if ($this->isGranted('ROLE_CANDIDATE')) {
            return $this->render('dashboard/candidate.html.twig');
        }

        // Check if the user is a recruiter
        if ($this->isGranted('ROLE_RECRUITER')) {
            return $this->render('dashboard/recruiter.html.twig');
        }

        // If no role is granted, redirect to login
        return $this->redirectToRoute('app_login');
    }
}
