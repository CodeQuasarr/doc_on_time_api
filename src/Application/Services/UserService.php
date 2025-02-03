<?php

namespace App\Application\Services;

use App\Repository\DoctorInfoRepository;
use App\Repository\UserRepository;

class UserService extends BaseServices
{

    public function __construct(
        DoctorInfoRepository $doctorInfoRepository,
        private readonly UserRepository $userRepository
    )
    {
        parent::__construct($doctorInfoRepository);
    }

    public function getUsers(array $roles): array
    {
        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->userRepository->findAll();
        }
        return $this->userRepository->findByRole('ROLE_DOCTOR');
    }

    public function getPatients(): array
    {
        return $this->userRepository->findByRole('ROLE_PATIENT');
    }
}