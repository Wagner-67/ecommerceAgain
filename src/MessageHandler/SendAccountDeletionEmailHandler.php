<?php

namespace App\MessageHandler;

use App\Message\SendAccountDeletionEmailMessage;
use App\Service\EmailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendAccountDeletionEmailHandler
{

    public function __construct(
        private EmailService $emailService
    ) {}

    public function __invoke(SendAccountDeletionEmailMessage $message): void
    {
        $this->emailService->sendAccountDeletionEmail(
            $message->email,
            $message->deleteUrl,
            $message->firstname
        );
    }

}