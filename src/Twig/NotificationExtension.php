<?php
// src/Twig/NotificationExtension.php
namespace App\Twig;

use App\Entity\Candidate;
use App\Entity\Notification;
use App\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class NotificationExtension extends AbstractExtension implements GlobalsInterface
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();
        $unreadCount = 0;

        if ($user instanceof Candidate) {
            $unreadCount = $this->entityManager->getRepository(Notification::class)
                ->count(['candidate' => $user, 'isRead' => false]);
        } elseif ($user instanceof Recruiter) {
            $unreadCount = $this->entityManager->getRepository(Notification::class)
                ->count(['recruiter' => $user, 'isRead' => false]);
        }

        return [
            'unread_notifications_count' => $unreadCount,
        ];
    }
}
