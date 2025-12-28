<?php

namespace App\Command;

use App\Entity\Notification;
use App\Entity\Candidate;
use App\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-subscription-renewals',
    description: 'Check for expiring subscriptions and create renewal notifications',
)]
class CheckSubscriptionRenewalsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Checking Subscription Renewals');

        $now = new \DateTimeImmutable();
        $warningDate = $now->modify('+7 days'); // Notify 7 days before expiry

        // Check candidates with expiring subscriptions
        $candidates = $this->entityManager->getRepository(Candidate::class)
            ->createQueryBuilder('c')
            ->where('c.subscription != :free')
            ->andWhere('c.subscriptionEndsAt IS NOT NULL')
            ->andWhere('c.subscriptionEndsAt <= :warningDate')
            ->andWhere('c.subscriptionEndsAt > :now')
            ->setParameter('free', 'free')
            ->setParameter('warningDate', $warningDate)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        // Check recruiters with expiring subscriptions
        $recruiters = $this->entityManager->getRepository(Recruiter::class)
            ->createQueryBuilder('r')
            ->where('r.subscription != :free')
            ->andWhere('r.subscriptionEndsAt IS NOT NULL')
            ->andWhere('r.subscriptionEndsAt <= :warningDate')
            ->andWhere('r.subscriptionEndsAt > :now')
            ->setParameter('free', 'free')
            ->setParameter('warningDate', $warningDate)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        $notificationsCreated = 0;

        // Create notifications for candidates
        foreach ($candidates as $candidate) {
            // Check if notification already exists for this renewal
            $existingNotification = $this->entityManager->getRepository(Notification::class)
                ->findOneBy([
                    'candidate' => $candidate,
                    'title' => 'Renouvellement d\'abonnement requis'
                ]);

            if (!$existingNotification) {
                $daysLeft = $now->diff($candidate->getSubscriptionEndsAt())->days;

                $notification = new Notification();
                $notification->setCandidate($candidate)
                    ->setTitle('Renouvellement d\'abonnement requis')
                    ->setMessage("Votre abonnement {$candidate->getSubscription()} expire dans {$daysLeft} jours. Pensez à le renouveler pour continuer à bénéficier de nos services.")
                    ->setIsRead(false);

                $this->entityManager->persist($notification);
                $notificationsCreated++;
            }
        }

        // Create notifications for recruiters
        foreach ($recruiters as $recruiter) {
            // Check if notification already exists for this renewal
            $existingNotification = $this->entityManager->getRepository(Notification::class)
                ->findOneBy([
                    'recruiter' => $recruiter,
                    'title' => 'Renouvellement d\'abonnement requis'
                ]);

            if (!$existingNotification) {
                $daysLeft = $now->diff($recruiter->getSubscriptionEndsAt())->days;

                $notification = new Notification();
                $notification->setRecruiter($recruiter)
                    ->setTitle('Renouvellement d\'abonnement requis')
                    ->setMessage("Votre abonnement {$recruiter->getSubscription()} expire dans {$daysLeft} jours. Pensez à le renouveler pour continuer à bénéficier de nos services.")
                    ->setIsRead(false);

                $this->entityManager->persist($notification);
                $notificationsCreated++;
            }
        }

        $this->entityManager->flush();

        $io->success("Created {$notificationsCreated} renewal notifications");

        return Command::SUCCESS;
    }
}