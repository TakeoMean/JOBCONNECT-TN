<?php

namespace App\Repository;

use App\Entity\TestQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestQuestion>
 */
class TestQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestQuestion::class);
    }

    // ------------------- Find Questions by Test -------------------
    /**
     * Find all questions associated with a specific test.
     *
     * @param int $testId The ID of the test.
     * @return TestQuestion[] Returns an array of TestQuestion objects.
     */
    public function findByTest(int $testId): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.test = :testId')
            ->setParameter('testId', $testId)
            ->orderBy('q.id', 'ASC')  // Optional sorting by question ID
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Questions by Test with Pagination -------------------
    /**
     * Get paginated questions for a specific test.
     *
     * @param int $testId The ID of the test.
     * @param int $page The current page for pagination.
     * @param int $limit The number of questions per page.
     * @return TestQuestion[] Returns an array of TestQuestion objects.
     */
    public function getPaginatedQuestions(int $testId, int $page = 1, int $limit = 10): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.test = :testId')
            ->setParameter('testId', $testId)
            ->setFirstResult(($page - 1) * $limit)  // Calculate the starting point for pagination
            ->setMaxResults($limit)  // Limit the number of results per page
            ->orderBy('q.id', 'ASC')  // Optional sorting by question ID
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find a Random Question for a Test -------------------
    /**
     * Find a random question from a specific test.
     *
     * @param int $testId The ID of the test.
     * @return TestQuestion|null Returns a random TestQuestion object or null if none found.
     */
    public function findRandomQuestion(int $testId): ?TestQuestion
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.test = :testId')
            ->setParameter('testId', $testId)
            ->setMaxResults(1)  // Limit to one result
            ->orderBy('RAND()')  // Random order
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ------------------- Count Questions for a Test -------------------
    /**
     * Count the number of questions associated with a specific test.
     *
     * @param int $testId The ID of the test.
     * @return int The number of questions.
     */
    public function countQuestionsByTest(int $testId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->andWhere('q.test = :testId')
            ->setParameter('testId', $testId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // ------------------- Find All Questions with Answers -------------------
    /**
     * Find all questions along with their associated answers for a specific test.
     *
     * @param int $testId The ID of the test.
     * @return TestQuestion[] Returns an array of TestQuestion objects with their answers.
     */
    public function findAllWithAnswers(int $testId): array
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.answers', 'a')  // Assuming 'answers' is the relation field in TestQuestion
            ->addSelect('a')  // Select answers along with questions
            ->andWhere('q.test = :testId')
            ->setParameter('testId', $testId)
            ->orderBy('q.id', 'ASC')  // Optional sorting by question ID
            ->getQuery()
            ->getResult();
    }
}
