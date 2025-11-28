<?php

namespace App\Service\DomainService;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserDeleteService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function deleteUserByToken(string $deleteToken, ?User $user): array
    {
        if (!$user) {
            return ['error' => 'You are not authorized', 'status' => 401];
        }

        if (!$deleteToken) {
            return ['error' => 'Delete token is required', 'status' => 400];
        }

        $userEntity = $this->em->getRepository(User::class)
            ->findOneBy(['deleteToken' => $deleteToken]);

        if (!$userEntity) {
            return ['error' => 'User not found', 'status' => 404];
        }

        if ($userEntity->getId() !== $user->getId()) {
            return ['error' => 'Access denied', 'status' => 403];
        }

        $this->em->remove($userEntity);
        $this->em->flush();

        return [
            'body' => ['success' => true, 'message' => 'User deleted successfully.'],
            'status' => 200
        ];
    }
}