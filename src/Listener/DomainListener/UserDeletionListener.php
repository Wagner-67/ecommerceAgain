<?php

namespace App\Listener\DomainListener;

use App\Event\UserDeletionRequestedEvent;
use App\Message\SendAccountDeletionEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: UserDeletionRequestedEvent::class, method: 'onUserDeletionRequested')]
class UserDeletionListener
{
    public function __construct(
        private RouterInterface $router,
        private MessageBusInterface $messageBus,
    ) {}

    public function onUserDeletionRequested(UserDeletionRequestedEvent $event): void
    {
        $user = $event->getUser();
        $deleteToken = $user->getDeleteToken();

        $deleteUrl = $this->router->generate(
            'api_account_deleted',
            ['deleteToken' => $deleteToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->messageBus->dispatch(
            new SendAccountDeletionEmailMessage(
                $user->getEmail(),
                $deleteUrl,
                $user->getFirstname()
            )
        );
    }
}