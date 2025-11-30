<?php

namespace App\Service\DomainService;

class PasswordResetService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function setPassword(array $data): array
    {

        if(!$deleteToken) {
            return [
                'body' => ['success' => false, 'errors' => 'Delete token is required.'],
                'status' => 422
            ];
        }

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['passwordResetToken' => $data['resetToken']]);

        if (!$user) {
            return ['error' => 'User not found', 'status' => 404];
        }
            
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

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'])
        );
        $user->setPasswordResetToken(null);

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
            'body' => ['success' => true, 'message' => 'Password reset successfully.'],
            'status' => 200
        ];
    }
}