<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_START', fields: ['start'])]
#[UniqueEntity(fields: ['start'], message: 'Il y a déjà une réservation à cette date')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[NotBlank(message: 'Le titre est obligatoire')]
    #[Groups(['reservation:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[NotBlank(message: 'La date de début est obligatoire')]
    #[Groups(['reservation:read'])]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[NotBlank(message: 'La date de fin est obligatoire')]
    #[Groups(['reservation:read'])]
    private ?\DateTimeInterface $end = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[NotBlank(message: 'Le client est obligatoire')]
    #[Groups(['reservation:read'])]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[NotBlank(message: 'Le coiffeur est obligatoire')]
    #[Groups(['reservation:read'])]
    private ?User $barber = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[Groups(['reservation:read'])]
    private ?Slot $slot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getBarber(): ?User
    {
        return $this->barber;
    }

    public function setBarber(?User $barber): static
    {
        $this->barber = $barber;

        return $this;
    }

    public function getSlot(): ?Slot
    {
        return $this->slot;
    }

    public function setSlot(?Slot $slot): static
    {
        $this->slot = $slot;

        return $this;
    }
}
