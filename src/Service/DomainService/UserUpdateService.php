<?php

namespace App\Service\DomainService;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserUpdateService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {}

    public function updateProfile(string $userId, array $data): array
    {

        if (!$user) {
            throw new AccessDeniedException('Authentication required');
        }

        if (!$this->security->isGranted('ROLE_USER', $user)) {
            throw new AccessDeniedException('Admin access required');
        }

        if(!$userId) {
            return ['error' => 'User ID is required', 'status' => 400];
        }

        if($user->getUserId() !== $userId) {
            return ['error' => 'Access denied', 'status' => 403];
        }
        
        $userEntity = $this->em->getRepository(User::class)->findOneBy(['userId' => $userId]);

        if(!$userEntity) {
            return ['error' => 'User not found', 'status' => 404];
        }

        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }

        if (isset($data['lastname'])) {
            $user->setLastname($data['lastname']);
        }

        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            return [
                'body' => ['success' => false, 'errors' => (string) $violations],
                'status' => 422
            ];
        }

        $this->em->persist($user);
        $this->em->flush();

        return [
            'body' => ['success' => true, 'message' => 'User profile updated successfully.'],
            'status' => 200
        ];
    }
}
