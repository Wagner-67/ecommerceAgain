<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\PasswordResetRequestedEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid; // ADD THIS IMPORT

class PasswordResetMail
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function SendResetMail(array $data): array
    {
        if(empty($data['email'])) {
            return [
                'body' => ['success' => false, 'errors' => 'Email is required.'],
                'status' => 422
            ];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'body' => ['success' => false, 'errors' => 'Invalid email format.'],
                'status' => 422
            ];
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return [
                'body' => ['success' => false, 'errors' => 'User with this email does not exist.'],
                'status' => 404
            ];
        }

        $PasswordResetToken = Uuid::v4()->toRfc4122();
        $user->setPasswordResetToken($PasswordResetToken);

        $this->em->persist($user);
        $this->em->flush();

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            new PasswordResetRequestedEvent($user), 
            PasswordResetRequestedEvent::class
        );

        return [
            'body' => ['success' => true, 'message' => 'Password reset mail sent successfully.'],
            'status' => 200
        ];
    }
}