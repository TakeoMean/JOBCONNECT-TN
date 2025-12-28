<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Candidate;
use App\Entity\Recruiter;
use App\Repository\NotificationRepository;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isRead = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Candidate::class, inversedBy: 'notifications')]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(targetEntity: Recruiter::class, inversedBy: 'notifications')]
    private ?Recruiter $recruiter = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    // --- Getters and Setters ---
    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }

    public function getIsRead(): bool { return $this->isRead; }
    public function setIsRead(bool $isRead): self { $this->isRead = $isRead; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getCandidate(): ?Candidate { return $this->candidate; }
    public function setCandidate(?Candidate $candidate): self { $this->candidate = $candidate; return $this; }

    public function getRecruiter(): ?Recruiter { return $this->recruiter; }
    public function setRecruiter(?Recruiter $recruiter): self { $this->recruiter = $recruiter; return $this; }
}
