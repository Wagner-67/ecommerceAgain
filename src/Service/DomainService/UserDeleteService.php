<?php

namespace App\Listener\DomainListener;

use App\Entity\User;

class UserDeleteService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function deleteUser(string $userId, ?User $user): array
    {
        if (!$user) {
            return ['error' => 'You are not authorized', 'status' => 401];
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

        $this->em->remove($userEntity);
        $this->em->flush();

        return [
            'body' => ['success' => true, 'message' => 'User deleted successfully.'],
            'status' => 200
        ];
    }
}