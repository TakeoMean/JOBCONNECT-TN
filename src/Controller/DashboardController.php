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
        if ($this->isGranted('ROLE_CANDIDATE')) {
            return $this->render('dashboard/candidate.html.twig');
        }
        if ($this->isGranted('ROLE_RECRUITER')) {
            return $this->render('dashboard/recruiter.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }
}