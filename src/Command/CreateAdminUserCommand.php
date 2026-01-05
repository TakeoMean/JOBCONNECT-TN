<?php
// src/Command/CreateAdminUserCommand.php
namespace App\Command;

use App\Entity\Admin;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin user',
)]
class CreateAdminUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password')
            ->addOption('full-name', null, InputOption::VALUE_OPTIONAL, 'Admin full name', 'Admin User')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $fullName = $input->getOption('full-name');

        // Check if user already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error(sprintf('User with email "%s" already exists.', $email));
            return Command::FAILURE;
        }

        // Create admin user
        $admin = new Admin();
        $admin->setEmail($email);
        $admin->setFullName($fullName);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
        $admin->setIsVerified(true);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success(sprintf('Admin user "%s" created successfully with email "%s"', $fullName, $email));

        return Command::SUCCESS;
    }
}