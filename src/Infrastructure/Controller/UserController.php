<?php

namespace App\Infrastructure\Controller;

use App\Application\Services\UserService;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct()
    {
    }
    #[Route('/api/me', name: 'app_user', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json($user, 200, [], ['groups' => ['User:after_login']]);
    }
}
