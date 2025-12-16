<?php
// src/Controller/CandidateController.php
namespace App\Controller;

use App\Entity\Candidate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class CandidateController extends AbstractController
{
    #[Route('/inscription/candidat', name: 'candidate_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $candidate = new Candidate();
            $candidate->setFullName($request->request->get('fullName'));
            $candidate->setEmail($request->request->get('email'));
            $candidate->setPhone($request->request->get('phone'));
            $candidate->setCity($request->request->get('city'));
            $candidate->setRoles(['ROLE_CANDIDATE']);

            $password = $request->request->get('password');
            $candidate->setPassword($hasher->hashPassword($candidate, $password));

            // --- PHOTO UPLOAD ---
            if ($photoFile = $request->files->get('photoFile')) {
                $candidate->setPhotoFile($photoFile); // triggers VichUploader
            }

            // --- CV UPLOAD ---
            if ($cvFile = $request->files->get('cvFile')) {
                $candidate->setCvFile($cvFile); // triggers VichUploader
            }

            $em->persist($candidate);
            $em->flush(); // files saved to /public/uploads automatically if VichUploader is configured

            $this->addFlash('success', 'Inscription réussie ! Bienvenue sur JobConnect TN !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('candidate/register.html.twig');
    }

    #[Route('/candidat/dashboard', name: 'candidate_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        return $this->render('candidate/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/candidat/profil', name: 'candidate_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        return $this->render('candidate/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
