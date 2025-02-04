<?php

namespace App\Domain\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface AvailabilityRepositoryInterface
{

    public function findAllAvailabilities(): array;

    public function findAvailabilitiesByDoctorWithPagination(int $doctorInfo, int $page, int $limit): Paginator;

    public function findDoctorAvailabilityForNextTwoDay(int $doctorInfo): array;

    public function findDoctorWeeklyAvailabilitiesAndSlots(int $doctorInfo, string $currentDate): array;
}