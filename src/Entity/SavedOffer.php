<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SavedOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'savedOffers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $offer = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $savedAt;

    public function __construct()
    {
        $this->savedAt = new \DateTimeImmutable();
    }

    public function getCandidate(): ?Candidate { return $this->candidate; }
    public function setCandidate(Candidate $candidate): self { $this->candidate = $candidate; return $this; }

    public function getOffer(): ?JobOffer { return $this->offer; }
    public function setOffer(JobOffer $offer): self { $this->offer = $offer; return $this; }

    public function getSavedAt(): \DateTimeImmutable { return $this->savedAt; }
    public function setSavedAt(\DateTimeImmutable $savedAt): self { $this->savedAt = $savedAt; return $this; }
}
