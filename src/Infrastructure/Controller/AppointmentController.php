<?php

namespace App\Infrastructure\Controller;

use App\Application\Services\AppointmentService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class AppointmentController extends AbstractController
{

    public function __construct(
        private readonly AppointmentService $appointmentService,
        SerializerInterface $serializer
    )
    { }

    /**
     * @throws Exception
     */
    #[Route('/api/appointments', name: 'app_appointment', methods: ['GET'])]
    public function getUserAppointments(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $date = $request->query->get('date');
        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 3);

        $response = $this->appointmentService->getAppointments($user, $page, $pageSize, $date);
        return $this->json($response, 200, [], ['groups' => 'Appointment:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/appointments/hours', name: 'app_appointment_hours', methods: ['GET'])]
    public function getTodayAppointHours(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $date = $request->query->get('date');

        $response = $this->appointmentService->getTodayAppointHours($user, $date);
        return $this->json($response, 200, [], ['groups' => 'Appointment:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/appointments/week', name: 'app_appointment_week', methods: ['GET'])]
    public function getWeeklyAppointmentsHoursAndDates(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->appointmentService->getAppointmentsDatesAndHoursForWeek($user);
        return $this->json($response, 200, [], ['groups' => 'Appointment:read']);
    }


//    /**
//     * @throws \Exception
//     */
//    #[Route('/api/availabilities', name: 'app_availability_create', methods: ['POST'])]
//    public function createDoctorAvailability(
//        Request $request,
//        #[MapRequestPayload] AvailabilityDTO $availability
//    ): JsonResponse
//    {
//        $user = $this->getUser();
//        $response = $this->availabilityService->createAvailability($availability, $user);
//        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
//    }
//
//    /**
//     * @throws \Exception
//     */
//    #[Route('/api/availabilities/{id}', name: 'app_availability_show', methods: ['PUT'])]
//    public function updateDoctorAvailability(
//        #[MapRequestPayload] AvailabilityDTO $availability, int $id
//    ): JsonResponse
//    {
//
//        $response = $this->availabilityService->updateAvailability($availability, $id);
//        return $this->json($response, 200, [], ['groups' => 'Availability:read']);
//    }
}
