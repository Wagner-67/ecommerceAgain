<?php

namespace App\Service;

use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use App\Message\SendAccountDeletionEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserDeleteMail
{
    public function __construct(
        private RouterInterface $router,
        private EmailService $emailService,
        private EntityManagerInterface $em,
        private MessageBusInterface $messageBus 
        ) {}

        public function onUserDeleted(UserDeletedEvent $event)
        {
            if(!$user) {
            return ['error' => 'You are not authorized', 'status' => 401];
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

            $deleteUrl = $this->router->generate(
                'api_account_deleted',
                ['deleteToken' => $user->getDeleteToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $this->messageBus->sendAccountDeletionEmail(
                new SendAccountDeletionEmailMessage(
                    $user->getEmail(),
                    $deleteUrl,
                    $user->getFirstname()
                )
            );

            return [
                'body' => ['success' => true, 'message' => 'Account deletion email sent successfully.'],
                'status' => 200
            ];
        }
}
