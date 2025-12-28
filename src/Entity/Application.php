<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Candidate;
use App\Entity\JobOffer;
use App\Entity\Test;

#[ORM\Entity]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Candidate::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(targetEntity: JobOffer::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $offer = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'Pending'; // Pending, Accepted, Refused

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $appliedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cvFilename = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $motivation = null;

    #[ORM\ManyToOne(targetEntity: Test::class)]
    private ?Test $assignedTest = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $testScore = null;

    // --- New Fields ---
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $city = null;

    public function __construct()
    {
        $this->appliedAt = new \DateTimeImmutable();
    }

    // --- Getters and Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;
        return $this;
    }

    public function getOffer(): ?JobOffer
    {
        return $this->offer;
    }

    public function setOffer(?JobOffer $offer): self
    {
        $this->offer = $offer;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getAppliedAt(): \DateTimeImmutable
    {
        return $this->appliedAt;
    }

    public function getCvFilename(): ?string
    {
        return $this->cvFilename;
    }

    public function setCvFilename(?string $cvFilename): self
    {
        $this->cvFilename = $cvFilename;
        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(?string $motivation): self
    {
        $this->motivation = $motivation;
        return $this;
    }

    public function getAssignedTest(): ?Test { return $this->assignedTest; }
    public function setAssignedTest(?Test $test): self { $this->assignedTest = $test; return $this; }

    public function getTestScore(): ?float { return $this->testScore; }
    public function setTestScore(?float $score): self { $this->testScore = $score; return $this; }

    // --- New Getters and Setters for phone and city ---
    
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }
}
