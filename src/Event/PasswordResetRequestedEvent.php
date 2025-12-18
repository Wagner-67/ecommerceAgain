<?php

namespace App\Event;

use App\Entity\User;

class PasswordResetRequestedEvent
{
    public function __construct(private User $user) {}

    public function getUser(): User
    {
        return $this->user;
    }
}