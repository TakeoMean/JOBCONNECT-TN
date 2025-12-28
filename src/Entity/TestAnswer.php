<?php

namespace App\Entity;

use App\Repository\TestAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestAnswerRepository::class)]
class TestAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $answer = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isCorrect = false;

    #[ORM\ManyToOne(targetEntity: TestQuestion::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestQuestion $question = null;

    public function getId(): ?int { return $this->id; }

    public function getAnswer(): ?string { return $this->answer; }
    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;
        return $this;
    }

    public function isCorrect(): bool { return $this->isCorrect; }
    public function setIsCorrect(bool $isCorrect): self
    {
        $this->isCorrect = $isCorrect;
        return $this;
    }

    public function getQuestion(): ?TestQuestion { return $this->question; }
    public function setQuestion(?TestQuestion $question): self
    {
        $this->question = $question;
        return $this;
    }
}
