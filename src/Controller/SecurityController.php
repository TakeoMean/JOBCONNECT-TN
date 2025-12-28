<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // ------------------- Login Page -------------------
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the last authentication error (if any)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Get the last entered username (if any)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render login template with last username and error if applicable
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // ------------------- Logout -------------------
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method is intercepted by the firewall, so it will never be called
        throw new \Exception('This should never be reached!');
    }
}
