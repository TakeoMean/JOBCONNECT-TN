<?php

namespace App\Repository;

use App\Entity\Recruiter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recruiter>
 */
class RecruiterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recruiter::class);
    }

    // ------------------- Find Recruiter by Email -------------------
    /**
     * Find a recruiter by email address.
     *
     * @param string $email The email address to search for.
     * @return Recruiter|null Returns a Recruiter object or null if not found.
     */
    public function findByEmail(string $email): ?Recruiter
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ------------------- Find Recruiters with Active Job Offers -------------------
    /**
     * Find all recruiters who have active job offers.
     *
     * @return Recruiter[] Returns an array of Recruiter objects who have active job offers.
     */
    public function findRecruitersWithActiveOffers(): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.jobOffers', 'j')  // Assuming "jobOffers" is the relation field on Recruiter
            ->andWhere('j.isPublished = true')  // Filters for active (published) job offers
            ->groupBy('r.id')  // Ensures unique recruiters in the result
            ->orderBy('r.organizationName', 'ASC')  // Sorting by organization name (adjust as needed)
            ->getQuery()
            ->getResult();
    }

    // ------------------- Search Recruiters by Company Name -------------------
    /**
     * Search for recruiters by company name.
     *
     * @param string $companyName The company name to search for.
     * @return Recruiter[] Returns an array of Recruiter objects.
     */
    public function searchByCompanyName(string $companyName): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.organizationName LIKE :companyName')
            ->setParameter('companyName', '%' . $companyName . '%')  // Partial match
            ->orderBy('r.organizationName', 'ASC')  // Sorting by organization name (adjust as needed)
            ->getQuery()
            ->getResult();
    }

    // ------------------- Paginate Recruiters -------------------
    /**
     * Get paginated results of recruiters.
     *
     * @param int $page The current page for pagination.
     * @param int $limit The number of results per page.
     * @return Recruiter[] Returns an array of Recruiter objects.
     */
    public function getPaginatedRecruiters(int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->setFirstResult(($page - 1) * $limit)  // Calculate the starting point for pagination
            ->setMaxResults($limit)  // Limit the number of results per page
            ->orderBy('r.organizationName', 'ASC');  // Optional sorting by organization name

        return $queryBuilder->getQuery()->getResult();
    }
}
