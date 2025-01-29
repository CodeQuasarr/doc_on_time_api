<?php

namespace App\Application\Services;

use App\Application\DTO\AvailabilityDTO;
use App\Entity\Availability;
use App\Entity\User;
use App\Repository\AvailabilityRepository;
use App\Repository\DoctorInfoRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class AvailabilityService
{

    public function __construct(
        private AvailabilityRepository $availabilityRepository,
        private DoctorInfoRepository   $doctorInfoRepository,
    )
    { }


    /**
     * @throws \Exception
     */
//    public function getAllAvailabilities(UserInterface $user): array
//    {
//        try {
//            $doctorInfo = $this->doctorInfoRepository->findOneBy(['doctor' => $user->getId()]);
//
//            if (is_null($doctorInfo)) {
//                throw new HttpException(Response::HTTP_NOT_FOUND, 'Doctor not found');
//            }
//            return $doctorInfo->getAvailabilities()->toArray();
//        } catch (\Exception $e) {
//            throw new \Exception($e->getMessage());
//        }
//    }

    public function getAllAvailabilities(UserInterface $user, int $page = 1, int $pageSize = 3): array
    {
        try {
            $doctorInfo = $this->doctorInfoRepository->findOneBy(['doctor' => $user->getId()]);

            if (is_null($doctorInfo)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, 'Doctor not found');
            }
            $paginator = $this->availabilityRepository->findAvailabilitiesByDoctorWithPagination($doctorInfo->getId(), $page, $pageSize);

            $availabilities = [];
            foreach ($paginator as $availability) {
                $availabilities[] = $availability;
            }

            return [
                'data' => $availabilities,
                'total' => count($paginator), // Nombre total d'éléments
                'page' => $page,
                'maxPage' => ceil(count($paginator) / $pageSize),
                'pageSize' => $pageSize,
            ];

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createAvailability(AvailabilityDTO $availabilityDTO, UserInterface $user): Availability
    {
        try {
            $doctorInfo = $this->doctorInfoRepository->findOneBy(['doctor' => $user->getId()]);

            if (is_null($doctorInfo)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, 'Doctor not found');
            }

            $availability = new Availability(
                $doctorInfo,
                $availabilityDTO->date,
                $availabilityDTO->slots
            );
            $this->availabilityRepository->save($availability);
            return $availability;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateAvailability(AvailabilityDTO $availabilityDTO, int $id): Availability
    {
        try {
            $availability = $this->availabilityRepository->find($id);
            if (is_null($availability)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, 'Availability not found');
            }
            $availability->setDate($availabilityDTO->date);
            $availability->setSlots($availabilityDTO->slots);
            $this->availabilityRepository->save($availability);
            return $availability;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }



}