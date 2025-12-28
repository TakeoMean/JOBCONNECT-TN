<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    // ------------------- Profile Page -------------------
    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        // Get the current logged-in user
        $user = $this->getUser();

        // Check if a user is logged in
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Redirect to login if no user is authenticated
        }

        // Render the profile page
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
