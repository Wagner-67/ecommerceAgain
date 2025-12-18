<?php

namespace App\Listener\DomainListener;

use App\Event\PasswordResetRequestedEvent;
use App\Message\SendPasswordResetEmailMessage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetRequestedEventListener 
{
    public function __construct(
        private RouterInterface $router,
        private MessageBusInterface $messageBus,
    ) {}

    public function onPasswordResetRequested(PasswordResetRequestedEvent $event): void
    {
        $user = $event->getUser();
        $resetToken = $user->getPasswordResetToken();

        $resetUrl = $this->router->generate(
            'api_password_reset_confirm',
            ['resetToken' => $resetToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->messageBus->dispatch(
            new SendPasswordResetEmailMessage(
                $user->getEmail(),
                $resetUrl,
                $user->getFirstname()
            )
        );
    }
}