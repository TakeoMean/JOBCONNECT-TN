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
    // ------------------- List Notifications -------------------
    #[Route('/notifications', name: 'notification_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Query notifications based on user role
        if ($user instanceof \App\Entity\Candidate) {
            $notifications = $em->getRepository(Notification::class)
                ->findBy(['candidate' => $user], ['createdAt' => 'DESC']);
        } elseif ($user instanceof \App\Entity\Recruiter) {
            $notifications = $em->getRepository(Notification::class)
                ->findBy(['recruiter' => $user], ['createdAt' => 'DESC']);
        } else {
            $notifications = [];
        }

        return $this->render('notification/list.html.twig', [
            'notifications' => $notifications,
            'unread_notifications_count' => count(array_filter($notifications, fn($n) => !$n->getIsRead()))
        ]);
    }

    // ------------------- Mark Notification as Read -------------------
    #[Route('/notification/{id}/read', name: 'notification_read')]
    public function read(Notification $notification, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Ensure the notification belongs to the current user
        $belongsToUser = false;
        if ($user instanceof \App\Entity\Candidate && $notification->getCandidate() === $user) {
            $belongsToUser = true;
        } elseif ($user instanceof \App\Entity\Recruiter && $notification->getRecruiter() === $user) {
            $belongsToUser = true;
        }

        if (!$belongsToUser) {
            throw $this->createAccessDeniedException('You cannot access this notification.');
        }

        // Mark notification as read
        $notification->setIsRead(true);
        $em->flush();

        $this->addFlash('success', 'Notification marked as read.');

        return $this->redirectToRoute('notification_list');
    }
    #[Route('/notifications/mark-all-read', name: 'notification_mark_all_read', methods: ['POST'])]
    public function markAllRead(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Query notifications based on user role and mark them as read
        if ($user instanceof \App\Entity\Candidate) {
            $em->getRepository(Notification::class)
                ->createQueryBuilder('n')
                ->update()
                ->set('n.isRead', ':read')
                ->where('n.candidate = :user')
                ->setParameter('read', true)
                ->setParameter('user', $user)
                ->getQuery()
                ->execute();
        } elseif ($user instanceof \App\Entity\Recruiter) {
            $em->getRepository(Notification::class)
                ->createQueryBuilder('n')
                ->update()
                ->set('n.isRead', ':read')
                ->where('n.recruiter = :user')
                ->setParameter('read', true)
                ->setParameter('user', $user)
                ->getQuery()
                ->execute();
        }

        $this->addFlash('success', 'Toutes les notifications ont été marquées comme lues.');

        return $this->redirectToRoute('notification_list');
    }
}
