<?php

namespace App\Application\Services;

use App\Application\DTO\AvailabilityDTO;
use App\Entity\Availability;
use App\Entity\DoctorInfo;
use App\Repository\AvailabilityRepository;
use App\Repository\DoctorInfoRepository;
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

    public function getDoctorNextTwoDaysAvailabilities(UserInterface $user): array
    {
        $doctorInfo = $this->getValidDoctorInfo($user);
        $availabilities = $this->availabilityRepository->findNextTwoDaysAvailabilityByDoctor($doctorInfo->getId());

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
     * @throws \DateMalformedStringException
     */
    public function getAvailabilitiesDatesAndHoursForWeek(UserInterface $user, $currentDate): array
    {
        $doctorInfo = $this->getValidDoctorInfo($user);
        $availabilities = $this->availabilityRepository->findAvailabilitiesDatesAndSlotsForWeek($doctorInfo->getId(), $currentDate);
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