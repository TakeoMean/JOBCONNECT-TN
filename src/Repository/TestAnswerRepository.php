<?php

namespace App\Repository;

use App\Entity\TestAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestAnswer>
 */
class TestAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestAnswer::class);
    }

    // ------------------- Find Answers by Question -------------------
    /**
     * Find all answers for a specific question.
     *
     * @param int $questionId The ID of the question.
     * @return TestAnswer[] Returns an array of TestAnswer objects.
     */
    public function findByQuestion(int $questionId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.question = :questionId')
            ->setParameter('questionId', $questionId)
            ->orderBy('a.id', 'ASC') // Sort by answer ID (can be adjusted)
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Answers by Correctness -------------------
    /**
     * Find answers that are correct or incorrect for a specific question.
     *
     * @param int $questionId The ID of the question.
     * @param bool $isCorrect Whether to fetch correct or incorrect answers.
     * @return TestAnswer[] Returns an array of TestAnswer objects.
     */
    public function findByCorrectness(int $questionId, bool $isCorrect): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.question = :questionId')
            ->andWhere('a.isCorrect = :isCorrect')
            ->setParameter('questionId', $questionId)
            ->setParameter('isCorrect', $isCorrect)
            ->orderBy('a.id', 'ASC') // Optional sorting by answer ID
            ->getQuery()
            ->getResult();
    }

    // ------------------- Find Answers by Test -------------------
    /**
     * Find all answers associated with a specific test.
     *
     * @param int $testId The ID of the test.
     * @return TestAnswer[] Returns an array of TestAnswer objects.
     */
    public function findByTest(int $testId): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.question', 'q')
            ->andWhere('q.test = :testId')
            ->setParameter('testId', $testId)
            ->orderBy('a.id', 'ASC') // Optional sorting
            ->getQuery()
            ->getResult();
    }

    // ------------------- Count Correct Answers for a Question -------------------
    /**
     * Count the number of correct answers for a specific question.
     *
     * @param int $questionId The ID of the question.
     * @return int The number of correct answers.
     */
    public function countCorrectAnswers(int $questionId): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.question = :questionId')
            ->andWhere('a.isCorrect = true')
            ->setParameter('questionId', $questionId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // ------------------- Count Correct Answers for a Test -------------------
    /**
     * Count the number of correct answers for all questions in a specific test.
     *
     * @param int $testId The ID of the test.
     * @return int The number of correct answers for the test.
     */
    public function countCorrectAnswersForTest(int $testId): int
    {
        return (int) $this->createQueryBuilder('a')
            ->join('a.question', 'q')
            ->select('COUNT(a.id)')
            ->andWhere('q.test = :testId')
            ->andWhere('a.isCorrect = true')
            ->setParameter('testId', $testId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
