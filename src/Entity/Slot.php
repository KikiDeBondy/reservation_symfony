<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_slot', columns: ['barber_id', 'date', 'start', 'end'])]
class Slot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'slots')]
    private ?User $barber_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $end = null;

    #[ORM\Column]
    private ?bool $is_reserved = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBarberId(): ?User
    {
        return $this->barber_id;
    }

    public function setBarberId(?User $barber_id): static
    {
        $this->barber_id = $barber_id;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function isReserved(): ?bool
    {
        return $this->is_reserved;
    }

    public function setIsReserved(bool $is_reserved): static
    {
        $this->is_reserved = $is_reserved;

        return $this;
    }
}
