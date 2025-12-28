<?php

namespace App\Entity;

use App\Repository\JobOfferRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Application;
use App\Entity\Test;  // Import the Test entity

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

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    #[ORM\OneToMany(mappedBy: 'jobOffer', targetEntity: Test::class, orphanRemoval: true)]
    private Collection $tests;  // Add this relationship to the Test entity

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->applications = new ArrayCollection();
        $this->tests = new ArrayCollection();  // Initialize the collection
    }

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(string $location): self { $this->location = $location; return $this; }

    public function getSalary(): ?int { return $this->salary; }
    public function setSalary(?int $salary): self { $this->salary = $salary; return $this; }

    public function getIsRemote(): bool { return $this->isRemote; }
    public function setIsRemote(bool $isRemote): self { $this->isRemote = $isRemote; return $this; }

    public function getContractType(): string { return $this->contractType; }
    public function setContractType(string $type): self { $this->contractType = $type; return $this; }

    public function isPublished(): bool { return $this->isPublished; }
    public function setIsPublished(bool $isPublished): self { $this->isPublished = $isPublished; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getRecruiter(): ?Recruiter { return $this->recruiter; }
    public function setRecruiter(?Recruiter $recruiter): self { $this->recruiter = $recruiter; return $this; }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection { return $this->applications; }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setOffer($this);
        }
        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            if ($application->getOffer() === $this) {
                $application->setOffer(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Test>
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests->add($test);
            $test->setJobOffer($this);  // Assuming that the 'Test' entity has a 'jobOffer' property.
        }
        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->removeElement($test)) {
            if ($test->getJobOffer() === $this) {
                $test->setJobOffer(null);  // Assuming the 'Test' entity has a 'jobOffer' property.
            }
        }
        return $this;
    }
}
