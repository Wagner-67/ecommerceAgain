<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;
use App\Event\UserDeletionRequestEvent;

class UserDeletionRequestEvent extends Event
{
    public function __construct(
        private User $user
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }
}