<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    // ------------------- Find Unread Notifications -------------------
    /**
     * Find unread notifications for a specific user.
     *
     * @param int $userId The ID of the user (Candidate or Recruiter).
     * @return Notification[] Returns an array of Notification objects.
     */
    public function findUnreadByUser(int $userId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.isRead = :isRead')
            ->andWhere('n.candidate = :userId OR n.recruiter = :userId')
            ->setParameter('isRead', false)
            ->setParameter('userId', $userId)
            ->orderBy('n.createdAt', 'DESC') // Optional sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Mark Notifications as Read -------------------
    /**
     * Mark notifications as read for a specific user.
     *
     * @param int $userId The ID of the user (Candidate or Recruiter).
     * @return void
     */
    public function markAsReadByUser(int $userId): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.isRead', ':isRead')
            ->where('n.isRead = :isRead')
            ->andWhere('n.candidate = :userId OR n.recruiter = :userId')
            ->setParameter('isRead', true)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }

    // ------------------- Find Notifications by User -------------------
    /**
     * Find all notifications related to a specific user (Candidate or Recruiter).
     *
     * @param int $userId The ID of the user (Candidate or Recruiter).
     * @return Notification[] Returns an array of Notification objects.
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.candidate = :userId OR n.recruiter = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('n.createdAt', 'DESC') // Optional sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Pagination for Notifications -------------------
    /**
     * Get paginated results of notifications based on a user.
     *
     * @param int $userId The ID of the user (Candidate or Recruiter).
     * @param int $page The current page for pagination.
     * @param int $limit The number of results per page.
     * @return Notification[] Returns an array of Notification objects.
     */
    public function getPaginatedNotifications(int $userId, int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('n')
            ->where('n.candidate = :userId OR n.recruiter = :userId')
            ->setParameter('userId', $userId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('n.createdAt', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
}
