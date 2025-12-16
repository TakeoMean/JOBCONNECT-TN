<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Candidate;
use App\Entity\JobOffer;

#[ORM\Entity]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Candidate::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(targetEntity: JobOffer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $offer = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $coverLetter = null;

    #[ORM\Column(length: 20)]
    private string $status = 'pending';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $appliedAt;

    public function __construct()
    {
        $this->appliedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getCandidate(): ?Candidate { return $this->candidate; }
    public function setCandidate(Candidate $candidate): self { $this->candidate = $candidate; return $this; }

    public function getOffer(): ?JobOffer { return $this->offer; }
    public function setOffer(JobOffer $offer): self { $this->offer = $offer; return $this; }

    public function getCoverLetter(): ?string { return $this->coverLetter; }
    public function setCoverLetter(?string $coverLetter): self { $this->coverLetter = $coverLetter; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getAppliedAt(): \DateTimeImmutable { return $this->appliedAt; }
    public function setAppliedAt(\DateTimeImmutable $appliedAt): self { $this->appliedAt = $appliedAt; return $this; }
}
