<?php

namespace App\Message;

final class SendVerificationEmailMessage
{
    public function __construct(
        public readonly string $email,
        public readonly string $verificationUrl,
        public readonly string $firstname
    ) {}
}