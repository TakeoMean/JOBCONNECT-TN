<?php
// src/Controller/JobOfferController.php
namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\Application;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class JobOfferController extends AbstractController
{
    #[Route('/offres', name: 'joboffer_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $offers = $em->getRepository(JobOffer::class)->findBy(['isPublished' => true], ['createdAt' => 'DESC']);
        return $this->render('joboffer/list.html.twig', ['offers' => $offers]);
    }

    #[Route('/offre/nouvelle', name: 'joboffer_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');

        if ($request->isMethod('POST')) {
            $offer = new JobOffer();
            $offer->setTitle($request->request->get('title'));
            $offer->setDescription($request->request->get('description'));
            $offer->setLocation($request->request->get('location'));
            $offer->setSalary($request->request->get('salary') ?: null);
            $offer->setRemote($request->request->has('remote'));
            $offer->setContractType($request->request->get('contractType'));
            $offer->setRecruiter($this->getUser());

            $em->persist($offer);
            $em->flush();

            $this->addFlash('success', 'Offre publiée avec succès !');
            return $this->redirectToRoute('joboffer_list');
        }

        return $this->render('joboffer/new.html.twig');
    }

    #[Route('/offre/{id}', name: 'joboffer_show')]
    public function show(JobOffer $offer): Response
    {
        return $this->render('joboffer/show.html.twig', ['offer' => $offer]);
    }

    #[Route('/offre/{id}/postuler', name: 'joboffer_apply')]
    public function apply(JobOffer $offer, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');

        $application = new Application();
        $application->setCandidate($this->getUser());
        $application->setOffer($offer);

        $notification = new Notification();
        $notification->setUser($offer->getRecruiter());
        $notification->setTitle("Nouvelle candidature !");
        $notification->setMessage($this->getUser()->getFullName() . " a postulé à votre offre : " . $offer->getTitle());

        $em->persist($application);
        $em->persist($notification);
        $em->flush();

        $this->addFlash('success', 'Candidature envoyée avec succès !');
        return $this->redirectToRoute('joboffer_list');
    }
}