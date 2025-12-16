<?php

namespace App\Entity;

use App\Repository\JobOfferRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    private ?int $salary = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isRemote = false;

    #[ORM\Column(length: 50)]
    private string $contractType = 'CDI';

    #[ORM\Column(type: 'boolean')]
    private bool $isPublished = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Recruiter::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recruiter $recruiter = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(string $location): self { $this->location = $location; return $this; }

    public function getSalary(): ?int { return $this->salary; }
    public function setSalary(?int $salary): self { $this->salary = $salary; return $this; }

    public function isRemote(): bool { return $this->isRemote; }
    public function setRemote(bool $isRemote): self { $this->isRemote = $isRemote; return $this; }

    public function getContractType(): string { return $this->contractType; }
    public function setContractType(string $type): self { $this->contractType = $type; return $this; }

    public function getRecruiter(): ?Recruiter { return $this->recruiter; }
    public function setRecruiter(?Recruiter $recruiter): self { $this->recruiter = $recruiter; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
