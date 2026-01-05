<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
#[ORM\DiscriminatorMap([
    "candidate" => Candidate::class,
    "recruiter" => Recruiter::class,
    "admin" => Admin::class
])]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $subscription = 'free';

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $subscriptionEndsAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    public function __construct()
    {
        // Child classes (Candidate/Recruiter) can safely call parent::__construct()
    }

    // --- Basic getters & setters ---
    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function eraseCredentials(): void {}

    // --- Full Name ---
    public function getFullName(): ?string { return $this->fullName; }
    public function setFullName(?string $fullName): self { $this->fullName = $fullName; return $this; }

    // --- Phone & City ---
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    // --- Subscription ---
    public function getSubscription(): string { return $this->subscription; }
    public function setSubscription(string $subscription): self { $this->subscription = $subscription; return $this; }

    public function getSubscriptionEndsAt(): ?\DateTimeImmutable { return $this->subscriptionEndsAt; }
    public function setSubscriptionEndsAt(?\DateTimeImmutable $date): self { $this->subscriptionEndsAt = $date; return $this; }

    // --- Verification ---
    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): self { $this->isVerified = $isVerified; return $this; }
}
