<?php

namespace App\Service\DomainService;

use App\Entity\CartItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CartItemUpdateService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    public function updateCartItem(array $data, ?User $user, string $cartItemId): array
    {

        if (!$cartItemId) {
            return ['error' => 'cartItemId is required', 'status' => 400];
        }

        $cartItem = $this->em->getRepository(CartItem::class)
            ->findOneBy(['cartItemId' => $cartItemId]);

        if (!$cartItem) {

            return ['error' => 'Item not found', 'status' => 404];
        }

        if($cartItem->getUser() !== $user->getId()) {
            return ['error' => 'Not authorized', 'status' => 403];
        }

        if (isset($data['quantity']) && $data['quantity'] > 0) {

            $cartItem->setQuantity($data['quantity']);

            $this->em->persist($cartItem);
            $this->em->flush();

            return ['success' => 'Quantity updated', 'status' => 200];
        }

        return ['error' => 'nothing to change', 'status' => 400];
    }
}