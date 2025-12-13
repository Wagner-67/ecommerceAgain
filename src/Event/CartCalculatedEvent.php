<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class CartCalculatedEvent extends Event
{
    public const NAME = 'cart.calculated';
    
    private User $user;
    private array $cartItems;
    private float $totalPrice = 0.0;
    private int $totalItems = 0;
    
    public function __construct(User $user, array $cartItems)
    {
        $this->user = $user;
        $this->cartItems = $cartItems;
    }
    
    public function getUser(): User
    {
        return $this->user;
    }
    
    public function getCartItems(): array
    {
        return $this->cartItems;
    }
    
    public function setTotalPrice(float $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }
    
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }
    
    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
    }
    
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }
}