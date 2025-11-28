<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\User;

class UserRegisteredEvent extends Event
{
    public function __construct(
        private User $user
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }
}