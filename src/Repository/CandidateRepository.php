<?php

namespace App\Repository;

use App\Entity\Candidate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Candidate>
 */
class CandidateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidate::class);
    }

    // Example custom query
    public function findByEmail(string $email): ?Candidate
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ------------------- Find Candidates by City -------------------
    /**
     * Find candidates by city.
     *
     * @param string $city The city name to search for.
     * @return Candidate[] Returns an array of Candidate objects.
     */
    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.city = :city')
            ->setParameter('city', $city)
            ->orderBy('c.fullName', 'ASC') // Optional sorting by full name
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Candidates by Skills -------------------
    /**
     * Find candidates who have a specific skill.
     *
     * @param string $skill The skill to search for.
     * @return Candidate[] Returns an array of Candidate objects.
     */
    public function findBySkill(string $skill): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.skills LIKE :skill')
            ->setParameter('skill', '%' . $skill . '%')
            ->orderBy('c.fullName', 'ASC') // Optional sorting by full name
            ->getQuery()
            ->getResult();
    }

    // ------------------- Filter Candidates by Job Preferences -------------------
    /**
     * Filter candidates based on job preferences (e.g., full-time or part-time).
     *
     * @param string $contractType The type of contract to filter by (e.g., "CDI", "CDD").
     * @return Candidate[] Returns an array of Candidate objects.
     */
    public function findByContractType(string $contractType): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.contractType = :contractType')
            ->setParameter('contractType', $contractType)
            ->orderBy('c.fullName', 'ASC') // Optional sorting by full name
            ->getQuery()
            ->getResult();
    }

    // ------------------- Pagination for Candidates -------------------
    /**
     * Get paginated results of candidates based on a filter.
     *
     * @param int $page The current page for pagination.
     * @param int $limit The number of results per page.
     * @return array Returns an array of Candidate objects.
     */
    public function getPaginatedCandidates(int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('c.fullName', 'ASC'); // Optional sorting

        return $queryBuilder->getQuery()->getResult();
    }

    // ------------------- Custom Method for Sorting Candidates by Date -------------------
    /**
     * Get candidates sorted by their registration date.
     *
     * @param string $direction The direction to sort by (ASC or DESC).
     * @return Candidate[] Returns an array of Candidate objects.
     */
    public function findAllSortedByRegistrationDate(string $direction = 'DESC'): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', $direction)
            ->getQuery()
            ->getResult();
    }
}
