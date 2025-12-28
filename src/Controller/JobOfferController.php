<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\Application;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class JobOfferController extends AbstractController
{
    // ------------------- List Job Offers -------------------
    #[Route('/offres', name: 'joboffer_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $offers = $em->getRepository(JobOffer::class)
            ->findBy(['isPublished' => true], ['createdAt' => 'DESC']);
        
        return $this->render('joboffer/list.html.twig', [
            'offers' => $offers
        ]);
    }

    // ------------------- Create a New Job Offer -------------------
    #[Route('/offre/nouvelle', name: 'joboffer_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        if ($request->isMethod('POST')) {
            $offer = new JobOffer();
            $offer->setTitle($request->request->get('title'))
                  ->setDescription($request->request->get('description'))
                  ->setLocation($request->request->get('location'))
                  ->setSalary($request->request->get('salary') ?: null)
                  ->setRemote($request->request->has('remote'))
                  ->setContractType($request->request->get('contractType'))
                  ->setRecruiter($this->getUser())
                  ->setIsPublished(true);

            $em->persist($offer);
            $em->flush();

            $this->addFlash('success', 'Offre publiée avec succès !');
            return $this->redirectToRoute('joboffer_list');
        }

        return $this->render('joboffer/new.html.twig');
    }

    // ------------------- Show Job Offer Details -------------------
    #[Route('/offre/{id}', name: 'joboffer_show')]
    public function show(JobOffer $offer): Response
    {
        return $this->render('joboffer/show.html.twig', [
            'offer' => $offer
        ]);
    }

    // ------------------- Apply to a Job Offer -------------------
    #[Route('/offre/{id}/postuler', name: 'joboffer_apply')]
    public function apply(JobOffer $offer, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        // If GET request, redirect to the form
        if ($request->isMethod('GET')) {
            return $this->redirectToRoute('joboffer_apply_form', ['id' => $offer->getId()]);
        }

        // Handle POST request (legacy support)
        $candidate = $this->getUser();

        // Check if already applied
        $existing = $em->getRepository(Application::class)->findOneBy([
            'candidate' => $candidate,
            'offer' => $offer
        ]);
        if ($existing) {
            $this->addFlash('warning', 'Vous avez déjà postulé pour cette offre.');
            return $this->redirectToRoute('joboffer_list');
        }

        $application = new Application();
        $application->setCandidate($candidate)
                    ->setOffer($offer)
                    ->setStatus('Pending');

        // Handle CV file upload
        if ($cvFile = $request->files->get('cvFile')) {
            $cvFilename = uniqid() . '.' . $cvFile->guessExtension();
            try {
                $cvFile->move($this->getParameter('cv_directory'), $cvFilename);
                $application->setCvFilename($cvFilename);
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l\'upload du CV.');
            }
        }

        // Handle motivation text
        $motivation = $request->request->get('motivation');
        if ($motivation) {
            $application->setMotivation($motivation);
        }

        $em->persist($application);

        // Create notification for the recruiter
        $notification = new Notification();
        $notification->setRecruiter($offer->getRecruiter())
                     ->setTitle("Nouvelle candidature reçue")
                     ->setMessage($candidate->getFullName() . " a postulé à votre offre : " . $offer->getTitle())
                     ->setIsRead(false);

        $em->persist($notification);
        $em->flush();

        $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
        return $this->redirectToRoute('joboffer_list');
    }
}
