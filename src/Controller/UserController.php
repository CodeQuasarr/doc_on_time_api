<?php

namespace App\Controller;

use App\Application\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {
    }
    #[Route('/api/me', name: 'app_user', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json($user, 200, [], ['groups' => ['User:after_login']]);
    }

    #[Route('/api/users', name: 'app_user_update', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        $roles = $this->getUser()->getRoles();
        $users = $this->userService->getUsers($roles);

        return $this->json($users, 200, [], ['groups' => ['User:after_login']]);
    }

    #[Route('/api/users/patients', name: 'app_user_patient', methods: ['GET'])]
    public function getPatients(): JsonResponse
    {
        $roles = $this->getUser()->getRoles();
        $patients = $this->userService->getPatients($roles);

        return $this->json($patients, 200, [], ['groups' => ['User:after_login']]);
    }
}
