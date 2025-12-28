<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SubscriptionController extends AbstractController
{
    // ------------------- Display Candidate Subscription Plans -------------------
    #[Route('/abonnement/candidat', name: 'candidate_plans')]
    #[IsGranted('ROLE_CANDIDATE')]
    public function candidatePlans(): Response
    {
        return $this->render('subscription/candidate_plans.html.twig');
    }

    // ------------------- Display Recruiter Subscription Plans -------------------
    #[Route('/abonnement/recruteur', name: 'recruiter_plans')]
    #[IsGranted('ROLE_RECRUITER')]
    public function recruiterPlans(): Response
    {
        return $this->render('subscription/recruiter_plans.html.twig');
    }

    // ------------------- Fake Payment Form -------------------
    #[Route('/paiement/{type}/{plan}', name: 'fake_payment')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function payment(string $type, string $plan): Response
    {
        // Allowed plans for candidates and recruiters
        $allowed = [
            'candidat' => ['premium', 'premium_plus'],
            'recruteur' => ['pro', 'enterprise'],
        ];

        // Check if the type and plan are valid
        if (!isset($allowed[$type]) || !in_array($plan, $allowed[$type], true)) {
            throw $this->createNotFoundException("Plan invalide");
        }

        // Set price based on the selected plan
        $price = match ($type) {
            'candidat' => ($plan === 'premium' ? 150 : 290),
            'recruteur' => ($plan === 'pro' ? 490 : 990),
            default => 0,
        };

        return $this->render('subscription/payment.html.twig', [
            'type' => $type,
            'plan' => $plan,
            'price' => $price,
        ]);
    }

    // ------------------- Validate Payment and Update Database -------------------
    #[Route('/paiement/validation/{type}/{plan}', name: 'payment_validate', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function validatePayment(string $type, string $plan, EntityManagerInterface $em): Response
    {
        // Allowed plans for candidates and recruiters
        $allowed = [
            'candidat' => ['premium', 'premium_plus'],
            'recruteur' => ['pro', 'enterprise'],
        ];

        // Check if the type and plan are valid
        if (!isset($allowed[$type]) || !in_array($plan, $allowed[$type], true)) {
            throw $this->createNotFoundException("Plan invalide");
        }

        $user = $this->getUser();

        // Update user subscription details
        $user->setSubscription($plan);
        $expiry = new \DateTimeImmutable('+30 days');
        $user->setSubscriptionEndsAt($expiry);

        // Persist changes to the database
        $em->flush();

        // Redirect to success page
        return $this->redirectToRoute('payment_success', [
            'type' => $type,
            'plan' => $plan,
        ]);
    }

    // ------------------- Payment Success Page -------------------
    #[Route('/paiement/success/{type}/{plan}', name: 'payment_success')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function success(string $type, string $plan): Response
    {
        return $this->render('subscription/success.html.twig', [
            'type' => $type,
            'plan' => $plan,
        ]);
    }
}
