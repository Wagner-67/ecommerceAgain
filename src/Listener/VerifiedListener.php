<?php

namespace App\Listener\DomainListener;

use App\Entity\User;
use App\Service\EmailService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class VerifiedListener
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function __invoke(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');

        $protectedRoutes = [
            'api_user_create',
            'api_verify_email',
        ];

        if (!in_array($currentRoute, $protectedRoutes, true)) {
            $this->checkUserVerification();
        }
    }

    private function checkUserVerification(): void
    {
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$user->isVerified()) {
            $verificationUrl = $this->router->generate(
                'api_verify_email',
                ['verifyToken' => $user->getVerifiedToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $this->emailService->sendVerificationEmail(
                $user->getEmail(),
                $verificationUrl,
                $user->getFirstname()
            );

            throw new AccessDeniedHttpException(
                'You need to verify your account. A verification link has been sent to your email.'
            );
        }
    }
}
