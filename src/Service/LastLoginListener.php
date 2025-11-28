<?php

namespace App\Service;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsEventListener(event: 'security.authentication.success')]
final class LastLoginListener
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        if (method_exists($user, 'setLastLoginAt')) {
            $user->setLastLoginAt(new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin')));

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}