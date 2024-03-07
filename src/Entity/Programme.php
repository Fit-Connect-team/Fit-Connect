<?php

namespace App\Entity;

use App\Repository\ProgrammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProgrammeRepository::class)]
class Programme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Type cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Type cannot be longer than {{ limit }} characters')]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Duree cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Duree cannot be longer than {{ limit }} characters')]
    private ?string $duree = null;
    #[Assert\GreaterThanOrEqual("today", message: "The start date cannot be before today")]
    #[Assert\Type("\DateTimeInterface", message: 'Start date must be a valid date')]
    private ?\DateTimeInterface $startdate = null;

    #[Assert\NotBlank(message: 'coach  cannot be empty')]
    #[ORM\ManyToOne(inversedBy: 'programmes')]
    private ?Coach $Coach = null;


     



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(\DateTimeInterface $startdate): static
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getCoach(): ?Coach
    {
        return $this->Coach;
    }

    public function setCoach(?Coach $Coach): static
    {
        $this->Coach = $Coach;

        return $this;
    }



   
}
