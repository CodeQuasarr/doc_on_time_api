<?php

namespace App\Application\DTO;

use App\Entity\DoctorInfo;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
readonly class AppointmentDTO
{

    public function __construct(
        #[Assert\Type(type: User::class, message: "The doctor must be valid user.")]
        public ?User $doctor = null,

        #[Assert\Type(type: User::class, message: "The patient must be valid user.")]
        public ?User $patient = null,

        #[Assert\NotBlank(message: "The day field is required.")]
        #[Assert\Type(type: 'string', message: "The day must be a valid date like yyyy-mm-dd.")]
        public ?string     $date = null,

        #[Assert\NotBlank(message: "The hour field is required.")]
        #[Assert\Type(type: 'string', message: "The hour must be a valid hour like hh:mm.")]
        public ?string      $hour = null,

        #[Assert\NotBlank(message: "The status field is required.")]
        #[Assert\Choice(
            choices: ['pending', 'approved', 'rejected'],
            message: "The status must be one of the following: {{ choices }}"
        )]
        public ?string $status = null,

        #[Assert\NotBlank(message: "The type field is required.")]
        #[Assert\Choice(
            choices: ['consultation', 'examination'],
            message: "The type must be one of the following: {{ choices }}"
        )]
        public ?string $type = null,

        #[Assert\Type(type: 'string', message: "The reason must be a valid string.")]
        public ?string $reason = null,

        #[Assert\Type(type: 'string', message: "The note must be a valid string.")]
        public ?string $notes = null,
    )
    {}
}