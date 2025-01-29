<?php

namespace App\Application\DTO;

use App\Entity\DoctorInfo;
use Symfony\Component\Validator\Constraints as Assert;
readonly class AvailabilityDTO
{

    public function __construct(
        #[Assert\Type(type: DoctorInfo::class, message: "The doctor must be valid.")]
        public ?DoctorInfo $doctor_info = null,

        #[Assert\NotBlank(message: "The day field is required.")]
        #[Assert\Type(type: 'string', message: "The day must be a valid date.")]
        public ?string     $date = null,

        #[Assert\NotBlank(message: "The slots field is required.")]
        #[Assert\Type(type: 'array', message: "The slots must be a valid array.")]
        public ?array      $slots = []
    )
    {}


}