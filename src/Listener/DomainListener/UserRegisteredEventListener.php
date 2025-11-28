<?php

namespace App\Listener\DomainListener;

use App\Entity\User;
use App\Service\EmailService;
use App\Event\UserRegisteredEvent;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserRegisteredEventListener
{

    public function __construct(private RouterInterface $router, private EmailService $emailService)
    {
    }

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();

        $verificationUrl = $this->router->generate(
            'api_verify_email',
            ['verifyToken' => $user->getVerifyToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->emailService->sendVerificationEmail(
            $user->getEmail(),
            $verificationUrl,
            $user->getFirstname()
        );
    }
}