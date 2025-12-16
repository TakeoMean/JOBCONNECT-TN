<?php
// src/Controller/RecruiterController.php
namespace App\Controller;

use App\Entity\Recruiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class RecruiterController extends AbstractController
{
   // src/Controller/RecruiterController.php
#[Route('/inscription/recruteur', name: 'recruiter_register')]
public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
{
    if ($request->isMethod('POST')) {
        $recruiter = new Recruiter();
        $recruiter->setEmail($request->request->get('email'));
        $recruiter->setCompanyName($request->request->get('companyName'));
        $recruiter->setResponsiblePerson($request->request->get('responsiblePerson'));
        $recruiter->setSector($request->request->get('sector'));
        $recruiter->setAddress($request->request->get('address'));
        $recruiter->setRoles(['ROLE_RECRUITER']);

        // Hash password
        $password = $request->request->get('password');
        $recruiter->setPassword($hasher->hashPassword($recruiter, $password));

        // --- Logo Upload ---
        if ($logoFile = $request->files->get('logoFile')) {
            $recruiter->setLogoFile($logoFile); // triggers VichUploader and sets 'logo'
        }

        // --- Profile Photo Upload (optional) ---
        if ($photoFile = $request->files->get('photoFile')) {
            $recruiter->setPhotoFile($photoFile); // triggers VichUploader and sets 'photo'
        }

        // Important: set updatedAt to force Doctrine update
        $recruiter->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($recruiter);
        $em->flush(); // now filenames are saved in DB, files uploaded to /public/uploads

        $this->addFlash('success', 'Inscription envoyée ! Un admin validera votre compte bientôt.');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('recruiter/register.html.twig');
}


    #[Route('/recruteur/dashboard', name: 'recruiter_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        return $this->render('recruiter/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/recruteur/offre/nouvelle', name: 'recruiter_offer_new')]
    public function newOffer(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        return $this->render('recruiter/offer_new.html.twig');
    }
}
