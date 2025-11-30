<?php

namespace App\Listener\DomainListener;

use App\Event\PasswordResetRequestedEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Listener\DomainListener\PasswordResetListener;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: PasswordResetRequestedEvent::class, method: 'onPasswordResetRequested')]
class PasswordResetListener
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
            new SendPasswordResetEmail(
                $user->getEmail(),
                $resetUrl,
                $user->getFirstname()
            )
        );
    }
}