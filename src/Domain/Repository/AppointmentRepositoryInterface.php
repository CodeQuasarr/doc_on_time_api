<?php

namespace App\Domain\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface AppointmentRepositoryInterface
{

    public function findAllAppointments(): array;

    public function findAppointmentByUserWithPagination(int $userId, int $page, int $limit, string $date): Paginator;

    public function findDoctorAppointmentHoursForToday(int $doctorId, string $date): array;

    public function findDoctorWeeklyAppointments(int $doctorId): array;
}