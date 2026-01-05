<?php

namespace App\Entity;

use App\Repository\CandidateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Notification;
use App\Entity\Application;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
#[Vich\Uploadable]
class Candidate extends User
{
    // --- Photo ---
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[Vich\UploadableField(mapping: "candidate_photo", fileNameProperty: "photo")]
    private ?File $photoFile = null;

    // --- CV ---
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cvPath = null;

    #[Vich\UploadableField(mapping: "cv", fileNameProperty: "cvPath")]
    private ?File $cvFile = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $slug = null;

    // --- Notifications ---
    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    // --- Applications ---
    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    // --- Saved Offers ---
    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: SavedOffer::class, orphanRemoval: true)]
    private Collection $savedOffers;

    public function __construct()
    {
        parent::__construct();
        $this->notifications = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->savedOffers = new ArrayCollection();
    }

    // --- Photo methods ---
    public function setPhotoFile(?File $photoFile = null): self
    {
        $this->photoFile = $photoFile;
        if ($photoFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    // --- CV methods ---
    public function setCvFile(?File $cvFile = null): self
    {
        $this->cvFile = $cvFile;
        if ($cvFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getCvFile(): ?File
    {
        return $this->cvFile;
    }

    public function getCvPath(): ?string
    {
        return $this->cvPath;
    }

    public function setCvPath(?string $cvPath): self
    {
        $this->cvPath = $cvPath;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    // --- Notifications methods ---
    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setCandidate($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getCandidate() === $this) {
                $notification->setCandidate(null);
            }
        }
        return $this;
    }

    // --- Applications methods ---
    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setCandidate($this);
        }
        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            if ($application->getCandidate() === $this) {
                $application->setCandidate(null);
            }
        }
        return $this;
    }

    // --- Saved Offers methods ---
    /**
     * @return Collection<int, SavedOffer>
     */
    public function getSavedOffers(): Collection
    {
        return $this->savedOffers;
    }

    public function addSavedOffer(SavedOffer $savedOffer): self
    {
        if (!$this->savedOffers->contains($savedOffer)) {
            $this->savedOffers->add($savedOffer);
            $savedOffer->setCandidate($this);
        }
        return $this;
    }

    public function removeSavedOffer(SavedOffer $savedOffer): self
    {
        if ($this->savedOffers->removeElement($savedOffer)) {
            if ($savedOffer->getCandidate() === $this) {
                $savedOffer->setCandidate(null);
            }
        }
        return $this;
    }

    // --- Helper ---
}
