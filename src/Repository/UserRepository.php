<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // ------------------- Find User by Email -------------------
    /**
     * Find a user by their email.
     *
     * @param string $email The email address to search for.
     * @return User|null Returns a User object or null if not found.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ------------------- Find Users by Role -------------------
    /**
     * Find all users with a specific role.
     *
     * @param string $role The role to search for (e.g., 'ROLE_CANDIDATE').
     * @return User[] Returns an array of User objects with the specified role.
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%'.$role.'%')
            ->orderBy('u.id', 'ASC')  // Optional: Sort by ID or any other field
            ->getQuery()
            ->getResult();
    }

    // ------------------- Search Users by Name -------------------
    /**
     * Search for users by their full name.
     *
     * @param string $name The name (or part of the name) to search for.
     * @return User[] Returns an array of User objects.
     */
    public function searchByName(string $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.fullName LIKE :name')
            ->setParameter('name', '%'.$name.'%')  // Partial match for the full name
            ->orderBy('u.fullName', 'ASC')  // Sorting by full name
            ->getQuery()
            ->getResult();
    }

    // ------------------- Count Users by Role -------------------
    /**
     * Count the number of users with a specific role.
     *
     * @param string $role The role to count (e.g., 'ROLE_CANDIDATE').
     * @return int The number of users with the specified role.
     */
    public function countByRole(string $role): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%'.$role.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // ------------------- Paginate Users -------------------
    /**
     * Get paginated users for a specific role.
     *
     * @param string $role The role to filter by (e.g., 'ROLE_CANDIDATE').
     * @param int $page The current page for pagination.
     * @param int $limit The number of results per page.
     * @return User[] Returns an array of User objects.
     */
    public function getPaginatedUsers(string $role, int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%'.$role.'%')
            ->setFirstResult(($page - 1) * $limit)  // Calculate the starting point for pagination
            ->setMaxResults($limit)  // Limit the number of results per page
            ->orderBy('u.id', 'ASC');  // Optional sorting by user ID

        return $queryBuilder->getQuery()->getResult();
    }
}
