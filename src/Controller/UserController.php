<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DomainService\UserProfileService;
use App\Listener\DomainListener\UserDeleteService;
use App\Listener\DomainListener\UserUpdateService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\DomainService\UserRegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/api/user', name: 'app_user_create', methods: ['POST'])]
    public function userRegister(
        Request $request,
        UserRegistrationService $userRegistrationService,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $result = $userRegistrationService->register($data);

        return new JsonResponse($result, Response::HTTP_CREATED);
    }

    #[Route('/api/user/profile/{userId}', name: 'app_user_read', methods: ['GET'])]
    public function userProfile(
        string $userId,
        UserProfileService $userProfileService,
    ): JsonResponse {

        $user = $this->getUser();

        $result = $userProfileService->getProfile($userId, $user);

        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);
    }

    #[Route('/api/user/profile/{userId}', name: 'app_user_update', methods: ['PATCH'])]
    public function userProfileUpdate(
        string $userId,
        Request $request,
        UserUpdateService $userUpdateService,
    ): JsonResponse {
        
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        $result = $userUpdateService->updateProfile($userId, $data, $user);

        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);

    }

    #[Route('/api/user/profile/{userId}', name: 'app_user_delete', methods: ['DELETE'])]
    public function userDelete(
        string $userId,
        EntityManagerInterface $em,
        UserDeleteMail $userDeleteService,
    ): JsonResponse {

        $user = $this->getUser();

        $result = $userDeleteMail->deleteUser($userId, $user);

        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);
    }
}
