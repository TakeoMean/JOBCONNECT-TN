<?php

namespace App\Repository;

use App\Entity\Test;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Test>
 */
class TestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Test::class);
    }

    // ------------------- Find Tests by Recruiter -------------------
    /**
     * Find all tests created by a specific recruiter.
     *
     * @param int $recruiterId The ID of the recruiter.
     * @return Test[] Returns an array of Test objects.
     */
    public function findByRecruiter(int $recruiterId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.recruiter = :recruiterId')
            ->setParameter('recruiterId', $recruiterId)
            ->orderBy('t.createdAt', 'DESC')  // Sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Tests by Title -------------------
    /**
     * Search tests by title.
     *
     * @param string $title The title or part of the title to search for.
     * @return Test[] Returns an array of Test objects.
     */
    public function findByTitle(string $title): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.title LIKE :title')
            ->setParameter('title', '%' . $title . '%')  // Partial match
            ->orderBy('t.createdAt', 'DESC')  // Sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Active Tests -------------------
    /**
     * Find all active tests.
     *
     * @return Test[] Returns an array of Test objects.
     */
    public function findActiveTests(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('t.createdAt', 'DESC')  // Sorting by creation date
            ->getQuery()
            ->getResult();
    }

    // ------------------- Count Tests by Recruiter -------------------
    /**
     * Count the number of tests created by a specific recruiter.
     *
     * @param int $recruiterId The ID of the recruiter.
     * @return int The number of tests created by the recruiter.
     */
    public function countTestsByRecruiter(int $recruiterId): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.recruiter = :recruiterId')
            ->setParameter('recruiterId', $recruiterId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // ------------------- Find Latest Tests -------------------
    /**
     * Find a test with all questions and answers loaded.
     *
     * @param int $testId The ID of the test.
     * @return Test|null Returns the Test object with questions and answers loaded.
     */
    public function findTestWithQuestionsAndAnswers(int $testId): ?Test
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.questions', 'q')
            ->leftJoin('q.answers', 'a')
            ->addSelect('q', 'a')
            ->andWhere('t.id = :testId')
            ->setParameter('testId', $testId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
