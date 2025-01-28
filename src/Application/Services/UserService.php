<?php

namespace App\Application\Services;

use App\Repository\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function getUsers(array $roles): array
    {
        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->userRepository->findAll();
        } elseif (in_array('ROLE_PATIENT', $roles)) {
            return $this->userRepository->findByRole('ROLE_DOCTOR');
        }
    }
}