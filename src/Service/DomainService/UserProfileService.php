<?php

namespace App\Service\DomainService;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserProfileService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getProfile(string $userId, ?User $user): ?array
    {

        if (!$user) {
            throw new AccessDeniedException('Authentication required');
        }

        if (!$this->security->isGranted('ROLE_USER', $user)) {
            throw new AccessDeniedException('USER access required');
        }

        if (!$userId) {
            return ['error' => 'User ID is required', 'status' => 400];
        }

        if ($user->getUserId() !== $userId) {
            return ['error' => 'Access denied', 'status' => 403];
        }

        $userEntity = $this->em->getRepository(User::class)->findOneBy(['userId' => $userId]);

        if (!$userEntity) {
            return ['error' => 'User not found', 'status' => 404];
        }

        return [
            'userId' => $userEntity->getUserId(),
            'email' => $userEntity->getEmail(),
            'firstname' => $userEntity->getFirstname(),
            'lastname' => $userEntity->getLastname(),
            'isVerified' => $userEntity->isVerified(),
            'lastLoginAt' => $userEntity->getLastLoginAt()?->format('Y-m-d H:i:s'),
            'createdAt' => $userEntity->getCreatedAt()->format('Y-m-d H:i:s'),
            'status' => 200
        ];
    }
}
