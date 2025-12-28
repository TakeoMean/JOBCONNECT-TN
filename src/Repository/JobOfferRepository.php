<?php

namespace App\Repository;

use App\Entity\JobOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobOffer>
 */
class JobOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }

    // ------------------- Find Job Offers by Recruiter -------------------
    /**
     * Find all job offers from a specific recruiter.
     *
     * @param int $recruiterId The ID of the recruiter.
     * @return JobOffer[] Returns an array of JobOffer objects.
     */
    public function findByRecruiter(int $recruiterId): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.recruiter = :recruiterId')
            ->setParameter('recruiterId', $recruiterId)
            ->orderBy('j.createdAt', 'DESC') // Order by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Job Offers by Location -------------------
    /**
     * Find job offers by location.
     *
     * @param string $location The location to filter by.
     * @return JobOffer[] Returns an array of JobOffer objects.
     */
    public function findByLocation(string $location): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.location = :location')
            ->setParameter('location', $location)
            ->orderBy('j.createdAt', 'DESC') // Optional sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Published Job Offers -------------------
    /**
     * Find all published job offers.
     *
     * @return JobOffer[] Returns an array of JobOffer objects.
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.isPublished = true')
            ->orderBy('j.createdAt', 'DESC') // Optional sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Job Offers Sorted by Date -------------------
    /**
     * Find all job offers, sorted by the creation date.
     *
     * @param string $direction Sort direction, either 'ASC' or 'DESC'.
     * @return JobOffer[] Returns an array of JobOffer objects.
     */
    public function findAllSortedByDate(string $direction = 'DESC'): array
    {
        return $this->createQueryBuilder('j')
            ->orderBy('j.createdAt', $direction)
            ->getQuery()
            ->getResult();
    }

    // ------------------- Search Job Offers by Keywords -------------------
    /**
     * Search job offers by a keyword in the title or description.
     *
     * @param string $keyword The keyword to search for in title or description.
     * @return JobOffer[] Returns an array of JobOffer objects.
     */
    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.title LIKE :keyword OR j.description LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('j.createdAt', 'DESC') // Optional sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // Example method to fetch job offers based on a custom field or condition
    /*
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
    */
}
