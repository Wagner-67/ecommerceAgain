<?php

namespace App\Service\DomainService;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Event\UserRegisteredEvent;

class UserRegistrationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function register(array $data): array
    {
        if (empty($data['confirm_password'])) {
            return [
                'body' => ['success' => false, 'errors' => 'Confirm password is required.'],
                'status' => 422
            ];
        }

        if ($data['password'] !== $data['confirm_password']) {
            return [
                'body' => ['success' => false, 'errors' => 'Passwords do not match.'],
                'status' => 422
            ];
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'])
        );

        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            return [
                'body' => ['success' => false, 'errors' => (string) $violations],
                'status' => 422
            ];
        }

        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new UserRegisteredEvent($user), UserRegisteredEvent::class);

        return [
            'body' => ['success' => true, 'message' => 'User registered successfully.'],
            'status' => 201
        ];
    }
}
