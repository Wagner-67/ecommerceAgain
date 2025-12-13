<?php

namespace App\Service\DomainService;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CartListService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
    ) {}
    
    public function listCart(?User $user): array
    {
        $userCart = $user->getCart();
        $cartItems = $this->em->getRepository(CartItem::class)->findBy(['cart' => $userCart]);

        $items = [];
        foreach ($cartItems as $cartItem) {
            $items[] = [
                'id' => $cartItem->getId(),
                'title' => $cartItem->getTitle(),
                'quantity' => $cartItem->getQuantity(),
                'price' => $cartItem->getPrice(),
                'created_at' => $cartItem->getCreatedAt()
            ];
        }

        $event = new CartCalculatedEvent($user, $items);
        $this->eventDispatcher->dispatch($event, CartCalculatedEvent::NAME);

        $userCart->setTotalPrice($event->getTotalPrice())
        $userCart->setTotalItems($event->getTotalItems())

        $this->em->persist($userCart);
        $this->em->flush();

        return [
            'items' => $items,
            'total_price' => $event->getTotalPrice(),
            'total_items' => $event->getTotalItems(),
            'status' => Response::HTTP_OK
        ];
    }
}