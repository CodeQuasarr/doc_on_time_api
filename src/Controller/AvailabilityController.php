<?php

namespace App\Controller;

use App\Application\DTO\AvailabilityDTO;
use App\Application\Services\AvailabilityService;
use App\Entity\Availability;
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
    #[Route('/api/availabilities', name: 'app_availability', methods: ['GET'])]
    public function getDoctorAvailabilities(): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->availabilityService->getAllAvailabilities($user);
        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/availabilities', name: 'app_availability_create', methods: ['POST'])]
    public function createDoctorAvailability(
        Request $request,
        #[MapRequestPayload] AvailabilityDTO $availability
    ): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->availabilityService->createAvailability($availability, $user);
        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/availabilities/{id}', name: 'app_availability_show', methods: ['PUT'])]
    public function updateDoctorAvailability(
        #[MapRequestPayload] AvailabilityDTO $availability, int $id
    ): JsonResponse
    {

        $response = $this->availabilityService->updateAvailability($availability, $id);
        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
    }
}
