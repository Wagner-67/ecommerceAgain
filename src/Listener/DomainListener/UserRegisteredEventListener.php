<?php

namespace App\Listener\DomainListener;

use App\Entity\User;
use App\Service\EmailService;
use App\Event\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use App\Message\SendVerificationEmailMessage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: UserRegisteredEvent::class, method: 'onUserRegistered')]
class UserRegisteredEventListener
{

    public function __construct(
        private RouterInterface $router,
        private EmailService $emailService,
        private MessageBusInterface $messageBus,
        private EntityManagerInterface $em   
         ) {}

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();

        $verificationUrl = $this->router->generate(
            'api_verify_email',
            ['verifyToken' => $user->getVerifiedToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

            $this->messageBus->dispatch(
                new SendVerificationEmailMessage(
                    $user->getEmail(),
                    $verificationUrl,
                    $user->getFirstname()
                )
            );

        $this->em->persist($user);
        $this->em->flush();

    }
}