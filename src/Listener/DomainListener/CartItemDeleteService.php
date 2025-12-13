<?php

namespace App\Service\DomainService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CartItemDeleteService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function deleteCartItem(?User $user, string $cartItemId): array
    {
        if(!$cartItemId) {
            return ['error' => 'cartItemId is requred']
        }

        $cartItem = $this->em->getRepository(CartItem::class)
            ->findOneBy(['cartItemId' => $cartItemId]);

        if (!$cartItem) {

            return ['error' => 'Item not found', 'status' => 404];
        }

        if($cartItem->getUser() !== $user->getId()) {
            return ['error' => 'Not authorized', 'status' => 403];
        }

        $this->em->remove($cartItem)
        $this->em->flush();

        return ['success' => 'Item Removed from cart', 'status' => 200];
    }

}