<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[HasLifecycleCallbacks]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Appointment:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    private ?user $doctor = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[\Symfony\Component\Serializer\Annotation\Groups(['Appointment:read'])]
    private ?user $patient = null;

    #[ORM\Column(length: 11)]
    #[Groups(['Appointment:read'])]
    private ?string $date = null;

    #[ORM\Column(length: 6)]
    #[Groups(['Appointment:read'])]
    private ?string $hour = null;

    #[ORM\Column(length: 50)]
    #[Groups(['Appointment:read'])]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    #[Groups(['Appointment:read'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Appointment:read'])]
    private ?string $reason = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updated_at = null;

    public function __construct(
        user $doctor,
        user $patient,
        string $date,
        string $hour,
        string $status,
        string $type,
        string $reason = null,
        string $notes = null
    )
    {
        $this->doctor = $doctor;
        $this->patient = $patient;
        $this->date = $date;
        $this->hour = $hour;
        $this->status = $status;
        $this->type = $type;
        $this->reason = $reason;
        $this->notes = $notes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDoctor(): ?user
    {
        return $this->doctor;
    }

    public function setDoctor(?user $doctor): static
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getPatient(): ?user
    {
        return $this->patient;
    }

    public function setPatient(?user $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHour(): ?string
    {
        return $this->hour;
    }

    public function setHour(string $hour): static
    {
        $this->hour = $hour;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
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

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->created_at = new DateTimeImmutable();
        $this->setUpdatedAt();
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }
}
