<?php

namespace App\Service\DomainService;

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

        //mhm

    }
}