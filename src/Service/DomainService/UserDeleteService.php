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
            throw new AccessDeniedException('Authentication required');
        }

        if (!$this->security->isGranted('ROLE_USER', $user)) {
            throw new AccessDeniedException('access required');
        }

        if (!$deleteToken) {
            return ['error' => 'Delete token is required', 'status' => 400];
        }

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['deleteToken' => $deleteToken]);

        if (!$user) {
            return ['error' => 'User not found', 'status' => 404];
        }

        if ($user->getDeleteToken() !== $deleteToken) {
            return ['error' => 'Access denied', 'status' => 403];
        }

        $this->em->remove($user);
        $this->em->flush();

        return [
            'body' => ['success' => true, 'message' => 'User deleted successfully.'],
            'status' => 200
        ];
    }
}