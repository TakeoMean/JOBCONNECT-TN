<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Application;
use App\Entity\Message;
use App\Entity\SavedOffer;
use App\Entity\JobOffer;
use App\Entity\Notification;
use App\Form\ApplicationType;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CandidateController extends AbstractController
{
    // ------------------- Registration -------------------
    #[Route('/inscription/candidat', name: 'candidate_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        $candidate = new Candidate();

        if ($request->isMethod('POST')) {
            $candidate->setFullName($request->request->get('fullName'))
                ->setEmail($request->request->get('email'))
                ->setPhone($request->request->get('phone'))
                ->setCity($request->request->get('city'))
                ->setRoles(['ROLE_CANDIDATE'])
                ->setPassword(
                    $hasher->hashPassword($candidate, $request->request->get('password'))
                );

            if ($photoFile = $request->files->get('photoFile')) {
                $candidate->setPhotoFile($photoFile);
            }

            if ($cvFile = $request->files->get('cvFile')) {
                $candidate->setCvFile($cvFile);
            }

            $candidate->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($candidate);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('candidate/register.html.twig');
    }

    // ------------------- Dashboard -------------------
    #[Route('/candidat/dashboard', name: 'candidate_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');
        $candidate = $this->getUser();

        return $this->render('candidate/dashboard.html.twig', [
            'applicationsCount' => $em->getRepository(Application::class)->count(['candidate' => $candidate]),
            'savedOffersCount' => $em->getRepository(SavedOffer::class)->count(['candidate' => $candidate]),
            'unreadMessagesCount' => (int) $em->getRepository(Message::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.receiver = :candidate')
                ->andWhere('m.isRead = false')
                ->setParameter('candidate', $candidate)
                ->getQuery()
                ->getSingleScalarResult(),
        ]);
    }

    // ------------------- Offers -------------------
    #[Route('/candidat/offres', name: 'candidate_offers_list')]
    public function offers(EntityManagerInterface $em): Response
    {
        return $this->render('candidate/offers.html.twig', [
            'offers' => $em->getRepository(JobOffer::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    // ------------------- Apply -------------------
    #[Route('/offre/{id}/postuler', name: 'joboffer_apply_form', methods: ['GET'])]
    public function applyForm(JobOffer $offer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);

        return $this->render('candidate/apply_offer.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/offre/{id}/postuler', name: 'joboffer_apply_submit', methods: ['POST'])]
    public function applySubmit(
        JobOffer $offer,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        $candidate = $this->getUser();

        // Prevent duplicate application
        $existing = $em->getRepository(Application::class)->findOneBy([
            'candidate' => $candidate,
            'offer' => $offer,
        ]);

        if ($existing) {
            return $this->redirectToRoute('candidate_applications_list');
        }

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('candidate/apply_offer.html.twig', [
                'offer' => $offer,
                'form' => $form->createView(),
            ]);
        }

        $application->setCandidate($candidate);
        $application->setOffer($offer);
        $application->setStatus('Pending');

        $cvFile = $form->get('cvFile')->getData();
        if ($cvFile) {
            $filename = uniqid().'.'.$cvFile->guessExtension();
            $cvFile->move($this->getParameter('cv_directory'), $filename);
            $application->setCvFilename($filename);
        }

        $em->persist($application);
        $em->flush();

        return $this->redirectToRoute('candidate_applications_list');
    }

    // ------------------- Applications list -------------------
    #[Route('/candidat/candidatures', name: 'candidate_applications_list')]
    public function applications(EntityManagerInterface $em): Response
    {
        return $this->render('candidate/applications.html.twig', [
            'applications' => $em->getRepository(Application::class)
                ->findBy(['candidate' => $this->getUser()], ['appliedAt' => 'DESC']),
        ]);
    }

    // ------------------- TAKE TEST -------------------
    #[Route('/candidat/test/{id}', name: 'candidate_take_test', requirements: ['id' => '\d+'])]
    public function takeTest(
        Application $application,
        Request $request,
        EntityManagerInterface $em,
        TestRepository $testRepository
    ): Response {
        $test = $application->getAssignedTest();

        if (!$test) {
            throw $this->createNotFoundException('Aucun test assigné à cette candidature.');
        }

        // Load test with all questions and answers to ensure they're available
        $test = $testRepository->findTestWithQuestionsAndAnswers($test->getId());

        if (!$test) {
            throw $this->createNotFoundException('Test introuvable.');
        }

        // Double-check that questions and answers are loaded
        $questions = $test->getQuestions();
        if ($questions->isEmpty()) {
            throw $this->createNotFoundException('Ce test ne contient aucune question.');
        }

        foreach ($questions as $question) {
            if ($question->getAnswers()->isEmpty()) {
                // This shouldn't happen with the new validation, but just in case
                throw $this->createNotFoundException('Une question ne contient aucune réponse.');
            }
        }

        // Ensure all answers are loaded for each question
        foreach ($test->getQuestions() as $question) {
            $question->getAnswers(); // This should trigger lazy loading if needed
        }

        if ($request->isMethod('POST')) {
            $score = 0;
            $total = count($test->getQuestions());

            foreach ($test->getQuestions() as $question) {
                $answerId = $request->request->get('question_' . $question->getId());

                foreach ($question->getAnswers() as $answer) {
                    if ($answer->getId() == $answerId && $answer->isCorrect()) {
                        $score++;
                    }
                }
            }

            $application->setTestScore(($score / max($total, 1)) * 100);
            $em->flush();

            return $this->redirectToRoute('candidate_test_result', ['id' => $application->getId()]);
        }

        return $this->render('candidate/take_test.html.twig', [
            'application' => $application,
            'questions' => $test->getQuestions(),
            'test' => $test,
        ]);
    }

    // ------------------- Test Result -------------------
    #[Route('/candidat/test/{id}/result', name: 'candidate_test_result')]
    public function testResult(Application $application): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        if ($application->getCandidate() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette candidature.');
        }

        return $this->render('candidate/test_result.html.twig', [
            'application' => $application,
        ]);
    }

    // ------------------- Submit Test -------------------
    #[Route('/candidat/test/{id}/submit', name: 'candidate_submit_test')]
    public function submitTest(Request $request, Application $application, EntityManagerInterface $em): Response
    {
        $test = $application->getAssignedTest();

        if (!$test) {
            throw $this->createNotFoundException('Aucun test assigné');
        }

        $score = 0;
        foreach ($test->getQuestions() as $question) {
            $answerId = $request->request->get('question_' . $question->getId());
            $answer = $question->getAnswers()->filter(function($answer) use ($answerId) {
                return $answer->getId() == $answerId;
            })->first();

            if ($answer && $answer->isCorrect()) {
                $score++;
            }
        }

        $application->setTestScore($score);
        $em->flush();

        return $this->redirectToRoute('candidate_test_result', ['id' => $application->getId()]);
    }

    // ------------------- Saved Offers -------------------
    #[Route('/candidat/offres-sauvegardees', name: 'candidate_saved_offers')]
    public function savedOffers(EntityManagerInterface $em): Response
    {
        return $this->render('candidate/saved_offers.html.twig', [
            'savedOffers' => $em->getRepository(SavedOffer::class)
                ->findBy(['candidate' => $this->getUser()]),
        ]);
    }

    // ------------------- Messages -------------------
    #[Route('/candidat/messages', name: 'candidate_messages_list')]
    public function messages(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');
        $candidate = $this->getUser();

        // Get received messages
        $messages = $em->getRepository(Message::class)
            ->findBy(['receiver' => $candidate], ['sentAt' => 'DESC']);

        // Get recruiters who have offers that this candidate has applied to
        $recruiters = $em->getRepository(\App\Entity\Recruiter::class)
            ->createQueryBuilder('r')
            ->join('r.jobOffers', 'o')
            ->join('o.applications', 'a')
            ->where('a.candidate = :candidate')
            ->setParameter('candidate', $candidate)
            ->distinct()
            ->getQuery()
            ->getResult();

        return $this->render('candidate/messages.html.twig', [
            'messages' => $messages,
            'recruiters' => $recruiters,
        ]);
    }

    // ------------------- Send Message -------------------
    #[Route('/candidat/message/send', name: 'candidate_send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        $recruiterId = $request->request->get('recruiter_id');
        $subject = $request->request->get('subject');
        $content = $request->request->get('content');

        if (!$recruiterId || !$content) {
            $this->addFlash('error', 'Tous les champs sont requis.');
            return $this->redirectToRoute('candidate_messages_list');
        }

        $recruiter = $em->getRepository(\App\Entity\Recruiter::class)->find($recruiterId);
        if (!$recruiter) {
            $this->addFlash('error', 'Recruteur introuvable.');
            return $this->redirectToRoute('candidate_messages_list');
        }

        // Check if candidate has applied to any of this recruiter's offers
        $hasApplied = $em->getRepository(Application::class)
            ->createQueryBuilder('a')
            ->join('a.offer', 'o')
            ->where('a.candidate = :candidate')
            ->andWhere('o.recruiter = :recruiter')
            ->setParameter('candidate', $this->getUser())
            ->setParameter('recruiter', $recruiter)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$hasApplied) {
            $this->addFlash('error', 'Vous ne pouvez envoyer des messages qu\'aux recruteurs dont vous avez postulé aux offres.');
            return $this->redirectToRoute('candidate_messages_list');
        }

        $message = new Message();
        $message->setSender($this->getUser())
                ->setReceiver($recruiter)
                ->setSubject($subject ?: 'Message de ' . $this->getUser()->getFullName())
                ->setContent($content)
                ->setIsRead(false)
                ->setSentAt(new \DateTimeImmutable());

        $em->persist($message);

        // Create notification for the recruiter
        $notification = new \App\Entity\Notification();
        $notification->setRecruiter($recruiter)
                    ->setTitle('Nouveau message reçu')
                    ->setMessage('Vous avez reçu un nouveau message de ' . $this->getUser()->getFullName())
                    ->setIsRead(false);

        $em->persist($notification);
        $em->flush();

        $this->addFlash('success', 'Message envoyé avec succès !');
        return $this->redirectToRoute('candidate_messages_list');
    }

    // ------------------- Mark Message as Read -------------------
    #[Route('/candidat/message/{id}/mark-read', name: 'candidate_mark_message_read', methods: ['POST'])]
    public function markMessageRead(Message $message, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        if ($message->getReceiver() !== $this->getUser()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        $message->setIsRead(true);
        $em->flush();

        return $this->json(['success' => true]);
    }

    // ------------------- Profile -------------------
    #[Route('/candidat/profil', name: 'candidate_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        return $this->render('candidate/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
