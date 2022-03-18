<?php

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
#[ORM\UniqueConstraint(name: "unique_grade_for_lesson", columns: ["grade", "person", "control"])]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $grade;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'grades')]
    #[ORM\JoinColumn(name:"person" , nullable: false)]
    private $person;

    #[ORM\ManyToOne(targetEntity: Control::class, inversedBy: 'grades')]
    #[ORM\JoinColumn(name: "control", nullable: false)]
    private $control;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrade(): ?float
    {
        return $this->grade;
    }

    public function setGrade(float $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getPerson(): ?User
    {
        return $this->person;
    }

    public function setPerson(?User $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getControl(): ?Control
    {
        return $this->control;
    }

    public function setControl(?Control $control): self
    {
        $this->control = $control;

        return $this;
    }
}
