<?php

namespace App\Controller;

use App\Entity\Test;
use App\Entity\Application;
use App\Form\TestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    // ------------------- Create a New Test -------------------
    #[Route('/recruteur/application/{id}/create-test', name: 'recruiter_create_test')]
    public function new(Request $request, Application $application, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        // Ensure the application is accepted before allowing test creation
        if ($application->getStatus() !== 'Accepted') {
            throw $this->createAccessDeniedException('You can only create a test for accepted applications');
        }

        // Create a new Test entity
        $test = new Test();
        $test->setRecruiter($this->getUser());
        $test->setJobOffer($application->getOffer()); // Assign the job offer related to the application

        // Create and handle the form
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set isCorrect for answers based on submitted data
            $requestData = $request->request->all();
            if (isset($requestData['test']['questions'])) {
                foreach ($requestData['test']['questions'] as $questionIndex => $questionData) {
                    if (isset($questionData['answers']) && isset($test->getQuestions()[$questionIndex])) {
                        $question = $test->getQuestions()[$questionIndex];
                        foreach ($questionData['answers'] as $answerIndex => $answerData) {
                            if (isset($question->getAnswers()[$answerIndex])) {
                                $answer = $question->getAnswers()[$answerIndex];
                                $isCorrect = isset($answerData['isCorrect']);
                                $answer->setIsCorrect($isCorrect);
                            }
                        }
                    }
                }
            }

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

    // ------------------- Show Test -------------------
    #[Route('/test/{id}', name: 'test_show', requirements: ['id' => '\d+'])]
    public function show(?Test $test): Response
    {
        if (!$test) {
            throw $this->createNotFoundException('Test not found');
        }

        return $this->render('test/show.html.twig', [
            'test' => $test,
        ]);
    }
}
