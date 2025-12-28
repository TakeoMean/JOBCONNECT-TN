<?php

namespace App\Controller;

use App\Entity\Recruiter;
use App\Entity\JobOffer;
use App\Entity\Application;
use App\Entity\Message;
use App\Entity\Candidate;
use App\Entity\Test;
use App\Entity\Notification;
use App\Form\TestType;
use App\Form\JobOfferType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class RecruiterController extends AbstractController
{
    /* ===================== REGISTER ===================== */
    #[Route('/inscription/recruteur', name: 'recruiter_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $recruiter = new Recruiter();
            $recruiter->setEmail($request->request->get('email'))
                ->setCompanyName($request->request->get('companyName'))
                ->setResponsiblePerson($request->request->get('responsiblePerson'))
                ->setSector($request->request->get('sector'))
                ->setAddress($request->request->get('address'))
                ->setRoles(['ROLE_RECRUITER'])
                ->setPassword($hasher->hashPassword($recruiter, $request->request->get('password')));

            // Handle logo and photo file uploads
            if ($logo = $request->files->get('logoFile')) {
                $recruiter->setLogoFile($logo);
            }
            if ($photo = $request->files->get('photoFile')) {
                $recruiter->setPhotoFile($photo);
            }

            $em->persist($recruiter);
            $em->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('recruiter/register.html.twig');
    }

    /* ===================== DASHBOARD ===================== */
    #[Route('/recruteur/dashboard', name: 'recruiter_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');
        $recruiter = $this->getUser();

        // Count job offers
        $offersCount = $em->getRepository(JobOffer::class)->count(['recruiter' => $recruiter]);

        // Count applications for this recruiter's offers
        $applicationsCount = $em->getRepository(Application::class)
            ->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->join('a.offer', 'o')
            ->where('o.recruiter = :r')
            ->setParameter('r', $recruiter)
            ->getQuery()
            ->getSingleScalarResult();

        // Count messages received
        $messagesCount = $em->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.receiver = :recruiter')
            ->setParameter('recruiter', $recruiter)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('recruiter/dashboard.html.twig', [
            'offersCount' => $offersCount,
            'applicationsCount' => $applicationsCount,
            'messagesCount' => $messagesCount,
            'user' => $recruiter,
        ]);
    }

    /* ===================== JOB OFFERS ===================== */
    #[Route('/recruteur/offres', name: 'recruiter_offers_list')]
    public function offersList(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        $offers = $em->getRepository(JobOffer::class)
            ->findBy(['recruiter' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('recruiter/offers_list.html.twig', [
            'offers' => $offers
        ]);
    }

    #[Route('/recruteur/offre/nouvelle', name: 'recruiter_offer_new')]
    public function newOffer(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        $offer = new JobOffer();
        $form = $this->createForm(JobOfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer->setRecruiter($this->getUser());
            $em->persist($offer);
            $em->flush();

            $this->addFlash('success', 'Offre créée avec succès !');
            return $this->redirectToRoute('recruiter_offers_list');
        }

        return $this->render('recruiter/offer_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /* ===================== APPLICATIONS PER OFFER ===================== */
    #[Route('/recruteur/offre/{id}/applications', name: 'recruiter_offer_applications')]
    public function offerApplications(JobOffer $offer, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        // Ensure tests are being loaded with the offer
        $offer = $em->getRepository(JobOffer::class)->find($offer->getId());

        if ($offer->getRecruiter() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $applications = $em->getRepository(Application::class)
            ->findBy(['offer' => $offer], ['appliedAt' => 'DESC']);

        return $this->render('recruiter/applications_list.html.twig', [
            'offer' => $offer,
            'applications' => $applications
        ]);
    }

    /* ===================== ACCEPT / REFUSE APPLICATION ===================== */
    #[Route('/recruteur/application/{id}/accept', name: 'recruiter_application_accept')]
    public function accept(Application $application, EntityManagerInterface $em): Response
    {
        // Set status based on whether test was taken
        if ($application->getTestScore() !== null) {
            $application->setStatus('Hired');
        } else {
            $application->setStatus('Accepted');
        }

        // Add notification
        $notif = new Notification();
        $notif->setCandidate($application->getCandidate())
              ->setTitle('Candidature acceptée')
              ->setMessage('Votre candidature a été acceptée.')
              ->setIsRead(false);

        $em->persist($notif);
        $em->flush();

        return $this->redirectToRoute('recruiter_candidatures');
    }

    #[Route('/recruteur/application/{id}/refuse', name: 'recruiter_application_refuse')]
    public function refuse(Application $application, EntityManagerInterface $em): Response
    {
        $application->setStatus('Refused');

        // Add notification
        $notif = new Notification();
        $notif->setCandidate($application->getCandidate())
              ->setTitle('Candidature refusée')
              ->setMessage('Votre candidature a été refusée.')
              ->setIsRead(false);

        $em->persist($notif);
        $em->flush();

        return $this->redirectToRoute('recruiter_candidatures');
    }

    /* ===================== CREATE TEST ===================== */
  #[Route('/recruteur/application/{id}/create-test', name: 'recruiter_create_test')]
public function createTest(Application $application, Request $request, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_RECRUITER');

    // Ensure the application is accepted before allowing test creation
    if ($application->getStatus() !== 'Accepted') {
        throw $this->createAccessDeniedException('You can only create a test for accepted applications');
    }

    // Create a new Test entity
    $test = new Test();
    $test->setRecruiter($this->getUser());
    $test->setJobOffer($application->getOffer()); // This line ensures that the test is linked to the job offer

    // Create and handle the form
    $form = $this->createForm(TestType::class, $test);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persist the test and associate it with the application
        $em->persist($test);
        $application->setAssignedTest($test); // Assign the created test to the application
        $em->flush();

        $this->addFlash('success', 'Test créé et assigné avec succès!');
        return $this->redirectToRoute('recruiter_offer_applications', ['id' => $application->getOffer()->getId()]);
    }

    // Render the form for creating the test
    return $this->render('recruiter/test_new.html.twig', [
        'form' => $form->createView(),
        'application' => $application,
    ]);
}



    /* ===================== ALL APPLICATIONS ===================== */
    #[Route('/recruteur/candidatures', name: 'recruiter_candidatures')]
    public function candidatures(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        $applications = $em->getRepository(Application::class)
            ->createQueryBuilder('a')
            ->join('a.offer', 'o')
            ->where('o.recruiter = :r')
            ->setParameter('r', $this->getUser())
            ->getQuery()
            ->getResult();

        return $this->render('recruiter/applications_list.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/recruteur/messages', name: 'recruiter_messages_list')]
    public function messages(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        $messages = $em->getRepository(Message::class)
            ->findBy(['receiver' => $this->getUser()], ['sentAt' => 'DESC']);

        // Get candidates who have applied to this recruiter's offers
        $candidates = $em->getRepository(Candidate::class)
            ->createQueryBuilder('c')
            ->join('c.applications', 'a')
            ->join('a.offer', 'o')
            ->where('o.recruiter = :recruiter')
            ->setParameter('recruiter', $this->getUser())
            ->distinct()
            ->getQuery()
            ->getResult();

        return $this->render('recruiter/messages_list.html.twig', [
            'messages' => $messages,
            'candidates' => $candidates,
        ]);
    }

    // ------------------- Send Message -------------------
    #[Route('/recruteur/message/send', name: 'recruiter_send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        $candidateId = $request->request->get('recipient');
        $subject = $request->request->get('subject');
        $content = $request->request->get('content');

        if (!$candidateId || !$content) {
            $this->addFlash('error', 'Tous les champs sont requis.');
            return $this->redirectToRoute('recruiter_messages_list');
        }

        $candidate = $em->getRepository(\App\Entity\Candidate::class)->find($candidateId);
        if (!$candidate) {
            $this->addFlash('error', 'Candidat introuvable.');
            return $this->redirectToRoute('recruiter_messages_list');
        }

        // Check if candidate has applied to any of this recruiter's offers
        $hasApplied = $em->getRepository(\App\Entity\Application::class)
            ->createQueryBuilder('a')
            ->join('a.offer', 'o')
            ->where('a.candidate = :candidate')
            ->andWhere('o.recruiter = :recruiter')
            ->setParameter('candidate', $candidate)
            ->setParameter('recruiter', $this->getUser())
            ->getQuery()
            ->getOneOrNullResult();

        if (!$hasApplied) {
            $this->addFlash('error', 'Vous ne pouvez envoyer des messages qu\'aux candidats qui ont postulé à vos offres.');
            return $this->redirectToRoute('recruiter_messages_list');
        }

        $message = new \App\Entity\Message();
        $message->setSender($this->getUser())
                ->setReceiver($candidate)
                ->setSubject($subject ?: 'Message de ' . $this->getUser()->getCompanyName())
                ->setContent($content)
                ->setIsRead(false)
                ->setSentAt(new \DateTimeImmutable());

        $em->persist($message);

        // Create notification for the candidate
        $notification = new \App\Entity\Notification();
        $notification->setCandidate($candidate)
                    ->setTitle('Nouveau message reçu')
                    ->setMessage('Vous avez reçu un nouveau message de ' . $this->getUser()->getCompanyName())
                    ->setIsRead(false);

        $em->persist($notification);
        $em->flush();

        $this->addFlash('success', 'Message envoyé avec succès !');
        return $this->redirectToRoute('recruiter_messages_list');
    }
}
