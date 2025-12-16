<?php
// src/Controller/TestController.php
namespace App\Controller;

use App\Entity\Test;
use App\Entity\TestQuestion;
use App\Entity\TestAnswer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class TestController extends AbstractController
{
    #[Route('/test/nouveau', name: 'test_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        if ($request->isMethod('POST')) {
            $test = new Test();
            $test->setTitle($request->request->get('title'));
            $test->setDuration($request->request->get('duration'));
            $test->setMinScore($request->request->get('minScore'));
            $test->setRecruiter($this->getUser());

            $question = new TestQuestion();
            $question->setQuestion($request->request->get('question'));
            $question->setTest($test);

            $answer1 = new TestAnswer();
            $answer1->setAnswer($request->request->get('answer1'));
            $answer1->setIsCorrect($request->request->get('correct') == '1');
            $answer1->setQuestion($question);

            $answer2 = new TestAnswer();
            $answer2->setAnswer($request->request->get('answer2'));
            $answer2->setIsCorrect($request->request->get('correct') == '2');
            $answer2->setQuestion($question);

            $em->persist($test);
            $em->flush();

            $this->addFlash('success', 'Test créé avec succès !');
        }

        return $this->render('test/new.html.twig');
    }
}