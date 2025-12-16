<?php
// src/Controller/NotificationController.php
namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notification_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('notification/list.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/notification/{id}/read', name: 'notification_read')]
    public function read(Notification $notification, EntityManagerInterface $em): Response
    {
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $notification->setIsRead(true);
        $em->flush();

        return $this->redirectToRoute('notification_list');
    }
}