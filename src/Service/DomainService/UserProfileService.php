<?php

namespace App\Service\DomainService;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserProfileService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function getProfile(User $user): array
    {
        $userEntity = $this->em->getRepository(User::class)
            ->findOneBy(['userId' => $user->getUserId()]);

        if (!$userEntity) {
            throw new NotFoundHttpException('User not found');
        }

        return [
            'userId'      => $userEntity->getUserId(),
            'email'       => $userEntity->getEmail(),
            'firstname'   => $userEntity->getFirstname(),
            'lastname'    => $userEntity->getLastname(),
            'isVerified'  => $userEntity->isVerified(),
            'lastLoginAt' => $userEntity->getLastLoginAt()?->format('Y-m-d H:i:s'),
            'createdAt'   => $userEntity->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
