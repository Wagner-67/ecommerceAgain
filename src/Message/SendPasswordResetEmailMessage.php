<?php

namespace App\Message;

final class SendPasswordResetEmailMessage
{
    public function __construct(
        public readonly string $email,
        public readonly string $resetUrl,
        public readonly string $firstname
    ) {}
}