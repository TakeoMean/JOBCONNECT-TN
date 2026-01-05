<?php

namespace App\Entity;

use App\Repository\RecruiterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\JobOffer;
use App\Entity\Notification;

#[ORM\Entity(repositoryClass: RecruiterRepository::class)]
#[Vich\Uploadable]
class Recruiter extends User
{
    #[ORM\Column(length: 255)]
    private ?string $organizationName = null;

    #[ORM\Column(length: 255)]
    private ?string $responsiblePerson = null;

    #[ORM\Column(length: 255)]
    private string $sector = '';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[Vich\UploadableField(mapping: "company_logo", fileNameProperty: "logo")]
    private ?File $logoFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[Vich\UploadableField(mapping: "user_photo", fileNameProperty: "photo")]
    private ?File $photoFile = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isApproved = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'recruiter', targetEntity: JobOffer::class, orphanRemoval: true)]
    private Collection $jobOffers;

    #[ORM\OneToMany(mappedBy: 'recruiter', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    public function __construct()
    {
        parent::__construct();
        $this->jobOffers = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }


    // --- Basic Info ---
    public function getOrganizationName(): ?string { return $this->organizationName; }
    public function setOrganizationName(string $organizationName): self { $this->organizationName = $organizationName; return $this; }

    public function getResponsiblePerson(): ?string { return $this->responsiblePerson; }
    public function setResponsiblePerson(string $responsiblePerson): self { $this->responsiblePerson = $responsiblePerson; return $this; }

    public function getSector(): string { return $this->sector; }
    public function setSector(string $sector): self { $this->sector = $sector; return $this; }

    public function getPhoneNumber(): ?string { return $this->phoneNumber; }
    public function setPhoneNumber(?string $phoneNumber): self { $this->phoneNumber = $phoneNumber; return $this; }

    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): self { $this->website = $website; return $this; }

    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): self { $this->isVerified = $isVerified; return $this; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(?string $slug): self { $this->slug = $slug; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    // --- Logo & Photo ---
    public function getLogo(): ?string { return $this->logo; }
    public function setLogo(?string $logo): self { $this->logo = $logo; return $this; }

    public function getLogoFile(): ?File { return $this->logoFile; }
    public function setLogoFile(?File $logoFile = null): self
    {
        $this->logoFile = $logoFile;
        if ($logoFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function getPhotoFile(): ?File { return $this->photoFile; }
    public function setPhotoFile(?File $photoFile = null): self
    {
        $this->photoFile = $photoFile;
        if ($photoFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    // --- Approval & Update ---
    public function isApproved(): bool { return $this->isApproved; }
    public function setIsApproved(bool $isApproved): self { $this->isApproved = $isApproved; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    // --- JobOffers ---
    /**
     * @return Collection<int, JobOffer>
     */
    public function getJobOffers(): Collection { return $this->jobOffers; }

    public function addJobOffer(JobOffer $jobOffer): self
    {
        if (!$this->jobOffers->contains($jobOffer)) {
            $this->jobOffers->add($jobOffer);
            $jobOffer->setRecruiter($this);
        }
        return $this;
    }

    public function removeJobOffer(JobOffer $jobOffer): self
    {
        if ($this->jobOffers->removeElement($jobOffer)) {
            if ($jobOffer->getRecruiter() === $this) {
                $jobOffer->setRecruiter(null);
            }
        }
        return $this;
    }

    // --- Notifications ---
    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection { return $this->notifications; }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setRecruiter($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getRecruiter() === $this) {
                $notification->setRecruiter(null);
            }
        }
        return $this;
    }
}
