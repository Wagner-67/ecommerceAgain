<?php

namespace App\Event;

use App\Entity\User;
use App\Event\PasswordResetRequestedEvent;
use Symfony\Contracts\EventDispatcher\Event;

class PasswordResetRequestedEvent extends Event
{
    public function __construct(
        private User $user
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }
}