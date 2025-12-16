<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionController extends AbstractController
{
    #[Route('/abonnement/candidat', name: 'candidate_subscription')]
    public function candidatePlans(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');
        return $this->render('subscription/candidate_plans.html.twig');
    }

    #[Route('/abonnement/recruteur', name: 'recruiter_subscription')]
    public function recruiterPlans(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');
        return $this->render('subscription/recruiter_plans.html.twig');
    }

    #[Route('/paiement/{type}/{plan}', name: 'fake_payment')]
    public function payment(string $type, string $plan): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('subscription/payment.html.twig', [
            'type' => $type,
            'plan' => $plan,
            'price' => $type === 'candidat'
                ? ($plan === 'premium' ? 150 : 290)
                : ($plan === 'pro' ? 490 : 990),
        ]);
    }

    #[Route('/paiement/success/{type}/{plan}', name: 'subscription_success')]
    public function success(string $type, string $plan, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $plans = [
            'candidat' => ['premium', 'premium_plus'],
            'recruteur' => ['pro', 'enterprise'],
        ];

        if (!isset($plans[$type]) || !in_array($plan, $plans[$type], true)) {
            throw $this->createNotFoundException('Plan invalide');
        }

        $user = $this->getUser();

        $user->setSubscription($plan);
        $user->setSubscriptionEndsAt(
            (new \DateTimeImmutable())->modify('+30 days')
        );

        $em->flush();

        $this->addFlash('success', 'Abonnement activé avec succès !');

        // Redirect based on role
        return $this->redirectToRoute(
            in_array('ROLE_RECRUITER', $user->getRoles(), true)
                ? 'recruiter_dashboard'
                : 'candidate_dashboard'
        );
    }
}
