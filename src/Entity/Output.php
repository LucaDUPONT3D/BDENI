<?php

namespace App\Entity;

use App\Repository\OutputRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutputRepository::class)]
class Output
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idOutput = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTimeStart = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxNumberRegistrations = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $exitInfo = null;

    public function getId(): ?int
    {
        return $this->idOutput;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateTimeStart(): ?\DateTimeInterface
    {
        return $this->dateTimeStart;
    }

    public function setDateTimeStart(\DateTimeInterface $dateTimeStart): self
    {
        $this->dateTimeStart = $dateTimeStart;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getMaxNumberRegistrations(): ?int
    {
        return $this->maxNumberRegistrations;
    }

    public function setMaxNumberRegistrations(?int $maxNumberRegistrations): self
    {
        $this->maxNumberRegistrations = $maxNumberRegistrations;

        return $this;
    }

    public function getExitInfo(): ?string
    {
        return $this->exitInfo;
    }

    public function setExitInfo(string $exitInfo): self
    {
        $this->exitInfo = $exitInfo;

        return $this;
    }
}
