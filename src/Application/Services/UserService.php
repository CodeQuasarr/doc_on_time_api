<?php

namespace App\Application\Services;

use App\Infrastructure\Persistence\DoctorInfoRepository;
use App\Infrastructure\Persistence\UserRepository;

class UserService extends BaseServices
{

    public function __construct(
        DoctorInfoRepository $doctorInfoRepository,
        private readonly UserRepository $userRepository
    )
    {
        parent::__construct($doctorInfoRepository);
    }

    /**
     * Retrieves the list of users based on the provided roles.
     * If the 'ROLE_ADMIN' role is present in the roles array, all users are returned.
     * Otherwise, users with the 'ROLE_DOCTOR' role are returned.
     *
     * @param array $roles The array of user roles to filter by.
     * @return array The array of users matching the specified roles.
     */
    public function getUsers(array $roles): array
    {
        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->userRepository->findAll();
        }
        return $this->userRepository->findByRole('ROLE_DOCTOR');
    }

    /**
     * Retrieves the list of users with the role of 'ROLE_PATIENT'.
     *
     * @return array The array of patients.
     */
    public function getPatients(): array
    {
        return $this->userRepository->findByRole('ROLE_PATIENT');
    }
}