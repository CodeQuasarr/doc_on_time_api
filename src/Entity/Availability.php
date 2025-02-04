<?php

namespace App\Entity;

use App\Infrastructure\Persistence\AvailabilityRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
#[HasLifecycleCallbacks]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Availability:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?DoctorInfo $doctor_info = null;

    #[ORM\Column(type: Types::STRING, length: 11)]
    #[Groups(['Availability:read'])]
    private ?string $date = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['Availability:read', 'Availability:write'])]
    private ?array $slots = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updated_at = null;

    public function __construct(doctorInfo $doctor_info, string $date, array $slots)
    {
        $this->doctor_info = $doctor_info;
        $this->date = $date;
        $this->slots = $slots;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDoctorInfo(): ?DoctorInfo
    {
        return $this->doctor_info;
    }

    public function setDoctorInfo(?DoctorInfo $doctor_info): static
    {
        $this->doctor_info = $doctor_info;

        return $this;
    }

    public function getDate(): ?string
    {
//        DateTime::createFromFormat('Y-m-d', $date);
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSlots(): array
    {
        return $this->slots;
    }

    public function setSlots(array $slots): static
    {
        $this->slots = $slots;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->created_at = new \DateTimeImmutable();
        $this->setUpdatedAt();
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }
}
