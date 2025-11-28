<?php

namespace App\Message;

final class SendAccountDeletionEmailMessage
{
    public function __construct(
        public readonly string $email,
        public readonly string $deleteUrl,
        public readonly string $firstname
    ) {}
}