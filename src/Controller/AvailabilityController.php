<?php

namespace App\Controller;

use App\Application\DTO\AvailabilityDTO;
use App\Application\Services\AvailabilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class AvailabilityController extends AbstractController
{

    public function __construct(
        private readonly AvailabilityService $availabilityService,
        SerializerInterface $serializer
    )
    { }

    /**
     * @throws \Exception
     */
    #[Route('/api/next-two-days-availabilities', name: 'app_next_two_availability', methods: ['GET'])]
    public function getNextTwoDaysAvailabilities(): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->availabilityService->getDoctorNextTwoDaysAvailabilities($user);

        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/availabilities', name: 'app_availability', methods: ['GET'])]
    public function getDoctorAvailabilities(Request $request): JsonResponse
    {
        $user = $this->getUser();

        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 3);

        $response = $this->availabilityService->getAllAvailabilities($user, $page, $pageSize);
        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/availabilities', name: 'app_availability_create', methods: ['POST'])]
    public function createDoctorAvailability( Request $request,  #[MapRequestPayload] AvailabilityDTO $availability): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->availabilityService->createAvailability($availability, $user);
        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/availabilities/{id}', name: 'app_availability_show', methods: ['PUT'])]
    public function updateDoctorAvailability(#[MapRequestPayload] AvailabilityDTO $availability, int $id): JsonResponse
    {
        $response = $this->availabilityService->updateAvailability($availability, $id);
        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/availabilities/week', name: 'app_Availability_week', methods: ['GET'])]
    public function getWeeklyAppointmentsHoursAndDates(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->availabilityService->getAvailabilitiesDatesAndHoursForWeek($user);
        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }
}
