<?php

namespace App\Service;

use App\Entity\User;
use App\Event\UserDeletionRequestedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserDeleteMail
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function deleteUser(string $userId, User $currentUser): array
    {
        if (!$currentUser) {
            return ['error' => 'You are not authorized', 'status' => 401];
        }

        if (!$userId) {
            return ['error' => 'User ID is required', 'status' => 400];
        }

        if ($currentUser->getUserId() !== $userId) {
            return ['error' => 'Access denied', 'status' => 403];
        }
        
        $userEntity = $this->em->getRepository(User::class)->findOneBy(['userId' => $userId]);

        if (!$userEntity) {
            return ['error' => 'User not found', 'status' => 404];
        }

        $deleteToken = Uuid::v4()->toRfc4122();
        $userEntity->setDeleteToken($deleteToken);

        $this->em->persist($userEntity);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new UserDeletionRequestedEvent($userEntity), UserDeletionRequestedEvent::class);

        return [
            'body' => ['success' => true, 'message' => 'Account deletion email sent successfully.'],
            'status' => 200
        ];
    }
}