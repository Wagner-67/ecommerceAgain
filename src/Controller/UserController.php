<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DomainService\UserProfileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\DomainService\UserRegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/api/user', name: 'app_user_create', methods: ['POST'])]
    public function userRegister(
        Request $request,
        UserRegistrationService $userRegistrationService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $result = $userRegistrationService->register($data);

        return new JsonResponse($result, Response::HTTP_CREATED);
    }

    #[Route('/api/user/{userId}', name: 'app_user_read', methods: ['GET'])]
    public function userProfile(
        string $userId,
        UserProfileService $userProfileService
    ): JsonResponse {

        $user = $this->getUser();

        $result = $userProfileService->getProfile($userId, $user);

        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);
    }
}
