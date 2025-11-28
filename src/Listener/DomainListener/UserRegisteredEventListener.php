<?php

namespace App\Listener\DomainListener;

use App\Entity\User;
use App\Service\EmailService;
use App\Event\UserRegisteredEvent;
use Symfony\Contracts\EventDispatcher\Event;
use App\Message\SendVerificationEmailMessage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserRegisteredEventListener
{

    public function __construct(
        private RouterInterface $router,
        private EmailService $emailService,
        private MessageBusInterface $messageBus
         ) {}

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();

        $verificationUrl = $this->router->generate(
            'api_verify_email',
            ['verifyToken' => $user->getVerifyToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

            $this->messageBus->dispatch(
                new SendVerificationEmailMessage(
                    $user->getEmail(),
                    $verificationUrl,
                    $user->getFirstname()
                )
            );

        $user->setLastVerificationEmailSentAt(new \DateTimeImmutable('now'));

        $this->em->presist($user);
        $this->em->flush();
    }
}