<?php
// src/Controller/RedirectController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    #[Route('/redirect-after-login', name: 'redirect_after_login')]
    public function redirectAfterLogin(): RedirectResponse
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }

        return $this->redirectToRoute('joboffer_list');
    }
}