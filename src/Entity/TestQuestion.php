<?php

namespace App\Entity;

use App\Repository\TestQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestQuestionRepository::class)]
class TestQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $question = null;

    #[ORM\Column(length: 50)]
    private string $type = 'multiple';

    #[ORM\ManyToOne(targetEntity: Test::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Test $test = null;

    #[ORM\OneToMany(
        mappedBy: 'question',
        targetEntity: TestAnswer::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        // Add 2 default answers for better form binding
        $this->answers->add(new TestAnswer());
        $this->answers->add(new TestAnswer());
    }

    // -------- GETTERS / SETTERS --------

    public function getId(): ?int { return $this->id; }

    public function getQuestion(): ?string { return $this->question; }
    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function getType(): string { return $this->type; }
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTest(): ?Test { return $this->test; }
    public function setTest(?Test $test): self
    {
        $this->test = $test;
        return $this;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(TestAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }
        return $this;
    }

    public function removeAnswer(TestAnswer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }
        return $this;
    }
}
