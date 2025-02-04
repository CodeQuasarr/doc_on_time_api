<?php

namespace App\Application\Services;

use App\Application\DTO\AvailabilityDTO;
use App\Entity\Availability;
use App\Infrastructure\Persistence\AvailabilityRepository;
use App\Infrastructure\Persistence\DoctorInfoRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class AvailabilityService extends BaseServices
{
    private const ERROR_AVAILABILITY_NOT_FOUND = 'Availability not found';

    public function __construct(
        DoctorInfoRepository $doctorInfoRepository,
        private AvailabilityRepository $availabilityRepository,
    )
    {
        parent::__construct($doctorInfoRepository);
    }

    /**
     * Retrieves paginated availabilities for a doctor based on the given user.
     *
     * @param UserInterface $user The user for which the doctor's availabilities are fetched.
     * @param int $page The current page number for pagination, defaults to 1.
     * @param int $pageSize The number of items per page for pagination, defaults to 3.
     *
     * @return array Returns an array containing the following keys:
     *               - 'data': The list of availabilities.
     *               - 'total': The total count of availabilities.
     *               - 'page': The current page number.
     *               - 'maxPage': The maximum number of pages.
     *               - 'pageSize': The size of each page.
     */
    public function getAllAvailabilities(UserInterface $user, int $page = 1, int $pageSize = 3): array
    {
        $doctorInfo = $this->getValidDoctorInfo($user);
        $paginator = $this->availabilityRepository->findAvailabilitiesByDoctorWithPagination($doctorInfo->getId(), $page, $pageSize);

        $availabilities = iterator_to_array($paginator);

        return [
            'data' => $availabilities,
            'total' => count($paginator),
            'page' => $page,
            'maxPage' => ceil(count($paginator) / $pageSize),
            'pageSize' => $pageSize,
        ];
    }

    /**
     * Retrieves the next two days of availabilities for a doctor based on the given user.
     *
     * This function calculates the next two days, retrieves the doctor's availability
     * data for those days, and formats it into a structured array. If no availabilities are
     * found for a specific day, it returns an object with empty slots for that day.
     *
     * @param UserInterface $user The user for which the doctor's next two days of availabilities are fetched.
     *
     * @return array Returns an array of formatted availabilities, each containing:
     *               - 'date': The availability date in 'Y-m-d' format.
     *               - 'slots': The list of available slots or an empty array if no slots are available for that date.
     */
    public function getDoctorNextTwoDaysAvailabilities(UserInterface $user): array
    {
        $doctorInfo = $this->getValidDoctorInfo($user);
        $availabilities = $this->availabilityRepository->findDoctorAvailabilityForNextTwoDay($doctorInfo->getId());

        $dates = [
            (new DateTime('today'))->format('Y-m-d'),
            (new DateTime('+1 day'))->format('Y-m-d'),
        ];

        $availabilityMapping = array_map(
            fn($date) => [
                'date' => $date,
                'slots' => array_values(array_map(fn($a) => [
                    'id' => $a->getId(),
                    'date' => $a->getDate(),
                    'slots' => $a->getSlots(),
                ], array_filter($availabilities, fn($a) => $a->getDate() === $date))),
            ],
            $dates
        );

        // Aplatir la structure pour fusionner toutes les dates et supprimer les dates vides
        $flatAvailability = [];
        foreach ($availabilityMapping as $availability) {
            if (count($availability['slots']) > 0) {
                $flatAvailability[] = $availability['slots'][0]; // Ajouter directement l'élément si des créneaux existent
            } else {
                // Ajouter un objet avec des slots vides pour les dates sans disponibilité
                $flatAvailability[] = [
                    'date' => $availability['date'],
                    'slots' => [],
                ];
            }
        }

        return array_values($flatAvailability);
    }

    /**
     * Creates a new availability entry for a doctor based on the given data and user.
     *
     * @param AvailabilityDTO $availabilityDTO DTO containing the date and slots for the availability.
     * @param UserInterface $user The user associated with the doctor for whom the availability is being created.
     *
     * @return Availability Returns the newly created availability instance.
     */
    public function createAvailability(AvailabilityDTO $availabilityDTO, UserInterface $user): Availability
    {
        $doctorInfo = $this->getValidDoctorInfo($user);

        $availability = new Availability(
            $doctorInfo,
            $availabilityDTO->date,
            $availabilityDTO->slots
        );

        $this->availabilityRepository->save($availability);

        return $availability;
    }

    /**
     * Updates the availability record with the provided data.
     *
     * @param AvailabilityDTO $availabilityDTO Data Transfer Object containing availability details.
     * @param int $id Identifier of the availability to be updated.
     *
     * @return Availability Updated availability entity.
     *
     * @throws HttpException If the availability is not found.
     */
    public function updateAvailability(AvailabilityDTO $availabilityDTO, int $id): Availability
    {
        $availability = $this->availabilityRepository->find($id);

        if (!$availability) {
            throw new HttpException(Response::HTTP_NOT_FOUND, self::ERROR_AVAILABILITY_NOT_FOUND);
        }

        $availability->setDate($availabilityDTO->date);
        $availability->setSlots($availabilityDTO->slots);

        $this->availabilityRepository->save($availability);

        return $availability;
    }

    /**
     * Retrieves the availability dates and slots for the current week for a given user.
     *
     * @param UserInterface $user The user for whom the availabilities are fetched.
     * @param mixed $currentDate The reference date to calculate the week's availabilities.
     *
     * @return array An array of availability data grouped by date for the week.
     * @throws \DateMalformedStringException
     */
    public function getAvailabilitiesDatesAndHoursForWeek(UserInterface $user, $currentDate): array
    {
        $doctorInfo = $this->getValidDoctorInfo($user);
        $availabilities = $this->availabilityRepository->findDoctorWeeklyAvailabilitiesAndSlots($doctorInfo->getId(), $currentDate);
        // Regrouper par date
        $groupedAvailabilities = [];
        // Générer les jours manquants pour la semaine
        $currentDate = (new \DateTime($currentDate, new \DateTimeZone('Europe/Paris')))->modify('monday this week');
        $cpt = 0;
        for ($i = 0; $i < 6; $i++) {
            $date = $currentDate->format('Y-m-d');
            $dates = array_column($availabilities, 'date');
            if (!in_array($date, $dates)) {
                $groupedAvailabilities[] = [
                    'date' => $date,
                    'slots' => [],
                ];
            } else {
                $groupedAvailabilities[] = [
                    'date' => $date,
                    'id' => $availabilities[$cpt]['id'],
                    'slots' => $availabilities[$cpt]['slots'],
                ];
                $cpt++;
            }
            $currentDate->modify('+1 day');
        }
        return array_values($groupedAvailabilities);
    }
}