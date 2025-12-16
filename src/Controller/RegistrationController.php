<?php
// src/Controller/RegistrationController.php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Recruiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

class RegistrationController extends AbstractController
{
    #[Route('/inscription/candidat', name: 'register_candidate')]
    public function candidate(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em, UploadHandler $uploader): Response
    {
        if ($request->isMethod('POST')) {
            $candidate = new Candidate();
            $candidate->setEmail($request->request->get('email'));
            $candidate->setFullName($request->request->get('fullName'));
            $candidate->setPhone($request->request->get('phone'));
            $candidate->setCity($request->request->get('city'));
            $candidate->setRoles(['ROLE_CANDIDATE']);
            $candidate->setPassword($hasher->hashPassword($candidate, $request->request->get('password')));
            $candidate->setPhotoFile($request->files->get('photoFile'));
            $candidate->setCvFile($request->files->get('cvFile'));

            $em->persist($candidate);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/candidate.html.twig');
    }

    #[Route('/inscription/recruteur', name: 'register_recruiter')]
    public function recruiter(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $recruiter = new Recruiter();
            $recruiter->setEmail($request->request->get('email'));
            $recruiter->setCompanyName($request->request->get('companyName'));
            $recruiter->setResponsiblePerson($request->request->get('responsiblePerson'));
            $recruiter->setRoles(['ROLE_RECRUITER']);
            $recruiter->setPassword($hasher->hashPassword($recruiter, $request->request->get('password')));
            $recruiter->setLogoFile($request->files->get('logoFile'));

            $em->persist($recruiter);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/recruiter.html.twig');
    }
}