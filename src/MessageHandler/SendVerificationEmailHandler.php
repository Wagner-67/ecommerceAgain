<?php

namespace App\MessageHandler;

use App\Message\SendVerificationEmailMessage;
use App\Service\EmailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendVerificationEmailHandler
{

    public function __construct(
        private EmailService $emailService
    ) {}

    public function __invoke(SendVerificationEmailMessage $message): void
    {
        $this->emailService->sendVerificationEmail(
            $message->email,
            $message->verificationUrl,
            $message->firstname
        );
    }
}
