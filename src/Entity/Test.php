<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private int $duration = 15;

    #[ORM\Column]
    private int $minScore = 70;

    #[ORM\ManyToOne(targetEntity: Recruiter::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recruiter $recruiter = null;

    #[ORM\OneToMany(mappedBy: 'test', targetEntity: TestQuestion::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDuration(): int { return $this->duration; }
    public function setDuration(int $duration): self { $this->duration = $duration; return $this; }

    public function getMinScore(): int { return $this->minScore; }
    public function setMinScore(int $minScore): self { $this->minScore = $minScore; return $this; }

    public function getRecruiter(): ?Recruiter { return $this->recruiter; }
    public function setRecruiter(?Recruiter $recruiter): self { $this->recruiter = $recruiter; return $this; }

    public function getQuestions(): Collection { return $this->questions; }
    public function addQuestion(TestQuestion $question): self { 
        if (!$this->questions->contains($question)) { 
            $this->questions->add($question); 
            $question->setTest($this); 
        } 
        return $this; 
    }
    public function removeQuestion(TestQuestion $question): self { 
        if ($this->questions->removeElement($question)) { 
            if ($question->getTest() === $this) { $question->setTest(null); } 
        } 
        return $this; 
    }
}
