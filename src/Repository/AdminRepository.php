<?php

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Admin>
 */
class AdminRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Admin) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Find an admin by their email address.
     *
     * @param string $email The email address to search for.
     * @return Admin|null Returns an Admin entity or null if not found.
     */
    public function findOneByEmail(string $email): ?Admin
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all admins with a certain role.
     *
     * @param string $role The role to search for.
     * @return Admin[] Returns an array of Admin objects.
     */
    public function findAllByRole(string $role): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all admins ordered by their last login date.
     *
     * @return Admin[] Returns an array of Admin objects.
     */
    public function findAllOrderedByLastLogin(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.lastLogin', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all admins who haven't logged in within a specific date range.
     *
     * @param \DateTimeInterface $startDate The start date for the search range.
     * @param \DateTimeInterface $endDate The end date for the search range.
     * @return Admin[] Returns an array of Admin objects.
     */
    public function findInactiveAdmins(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.lastLogin BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    // Example method to fetch admins based on a custom field or condition
    /*
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
    */
}
