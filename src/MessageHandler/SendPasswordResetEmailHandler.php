<?php

namespace App\MessageHandler;

use App\Service\EmailService;
use App\Message\SendPasswordResetEmailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendPasswordResetEmailHandler
{
    public function __construct(
        private EmailService $emailService
    ) {}

    public function __invoke(SendPasswordResetEmailMessage $message): void
    {
        $this->emailService->sendPasswordResetEmail(
            $message->email,
            $message->resetUrl,
            $message->firstname
        );
    }
}