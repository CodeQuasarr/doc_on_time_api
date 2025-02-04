<?php

namespace App\Application\Services;

use App\Domain\Repository\AppointmentRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class AppointmentService
{

    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
    )
    { }


    /**
     * Retrieves a paginated list of appointments for a given user.
     * Allows filtering by an optional date and returns pagination details.
     *
     * @param UserInterface $user The user for whom the appointments are being fetched.
     * @param int $page The current page of the paginated results (default is 1).
     * @param int $pageSize The number of appointments per page (default is 3).
     * @param string|null $date An optional date to filter the appointments.
     * @return array An array containing appointment data, pagination metadata, and the total count of appointments.
     * @throws \Exception When an error occurs while fetching or processing appointments.
     */
    public function getAppointments(UserInterface $user, int $page = 1, int $pageSize = 3, string $date = null): array
    {
        try {
            $paginator = $this->appointmentRepository->findAppointmentByUserWithPagination($user->getId(), $page, $pageSize, $date);

            $appointments = [];
            foreach ($paginator as $availability) {
                $appointments[] = $availability;
            }

            return [
                'data' => $appointments,
                'total' => count($paginator),
                'page' => $page,
                'maxPage' => ceil(count($paginator) / $pageSize),
                'pageSize' => $pageSize,
            ];

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieves appointment hours for a specific day for the given user.
     * Returns a list of time slots corresponding to the appointments on the specified date.
     *
     * @param UserInterface $user The user for whom the appointment hours are being fetched.
     * @param string $date The date for which the appointment hours are being retrieved, formatted as a string.
     * @return array A list of appointment hours for the specified date.
     * @throws \Exception When an error occurs during the fetching or processing of appointment hours.
     */
    public function getTodayAppointHours(UserInterface $user, string $date): array
    {
        try {
            return array_map(function($item) {
                return $item['hour'];
            }, $this->appointmentRepository->findDoctorAppointmentHoursForToday($user->getId(), $date));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieves appointments' dates and hours for a full week for the given user.
     * Groups appointments by date, associating each date with a list of time slots.
     *
     * @param UserInterface $user The user for whom the appointments are being fetched.
     * @return array A list of grouped appointments with their respective dates and time slots.
     * @throws \Exception When an error occurs during the fetching or processing of appointments.
     */
    public function getAppointmentsDatesAndHoursForWeek(UserInterface $user): array
    {
        try {
            $appointments = $this->appointmentRepository->findDoctorWeeklyAppointments($user->getId());
            $groupedAppointments = [];
            foreach ($appointments as $appointment) {
                $date = $appointment['date'];
                $hour = $appointment['hour'];

                // Si la date est déjà présente dans le tableau, on ajoute l'heure
                if (isset($groupedAppointments[$date])) {
                    $groupedAppointments[$date]['slots'][] = $hour;
                } else {
                    // Sinon, on initialise la date avec son heure
                    $groupedAppointments[$date] = [
                        'date' => $date,
                        'slots' => [$hour],
                    ];
                }
            }
            return array_values($groupedAppointments);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}