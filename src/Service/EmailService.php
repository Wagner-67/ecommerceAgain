<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendVerificationEmail(string $toEmail, string $verificationUrl, string $firstname): void
    {
        $html = "
            <html>
                <body>
                    <p>Dear {$forename},</p>
                    <p>Please verify your email by clicking the link below:</p>
                    <p><a href=\"{$verificationUrl}\">Verify Email</a></p>
                    <p>Best regards,<br/>The Team</p>
                </body>
            </html>
        ";

        $email = (new Email())
            ->to($toEmail)
            ->from('noreply@yourapp.com')
            ->subject('Verify your email')
            ->html($html);

        $this->mailer->send($email);
    }
}