<?php

namespace App\Listener\DomainListener;

use App\Event\CartCalculatedEvent;

class CartCalculationSubscriber
{
    public function onCartCalculated(CartCalculatedEvent $event): void
    {
        $cartItems = $event->getCartItems();
        
        $totalPrice = 0;
        $totalItems = 0;
        
        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity']; 
        }
        
        $event->setTotalPrice($totalPrice);
        $event->setTotalItems($totalItems);
    }
}