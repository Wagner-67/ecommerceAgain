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
                    <p>Dear {$firstname},</p>
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

    public function sendPasswordResetEmail(string $toEmail, string $resetUrl, string $firstname): void
    {
        $html = "
            <html>
                <body>
                    <p>Dear {$firstname},</p>
                    <p>You can reset your password by clicking the link below:</p>
                    <p><a href=\"{$resetUrl}\">Reset Password</a></p>
                    <p>If you did not request a password reset, please ignore this email.</p>
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

    public function sendAccountDeletionEmail(string $toEmail, string $deleteUrl, string $firstname): void
    {
        $html = "
            <html>
                <body>
                    <p>Dear {$firstname},</p>
                    <p>You requested to delete your account. To confirm, please click the link below:</p>
                    <p><a href=\"{$deleteUrl}\">Confirm Account Deletion</a></p>
                    <p>If you did not request this, please ignore the email.</p>
                    <p>Best regards,<br/>The Team</p>
                </body>
            </html>
        ";

        $email = (new Email())
            ->to($toEmail)
            ->from('noreply@yourapp.com')
            ->subject('Confirm your account deletion')
            ->html($html);

        $this->mailer->send($email);
    }
}