<?php

namespace App\Service\DomainService;

use App\Entity\Product;
use App\Entity\CartItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CartItemService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    public function addCartItem(array $data, ?User $user, strng $productId): array
    {

        if (!$productId) {
            return ['error' => 'productId is required', 'status' => 400];
        }

        $product = $this->em->getRepository(Product::class)
            ->findOneBy(['productId'=> $productId]);

        if(!$product) {
            return ['error'=>'Product not found', 'status'=> 404];
        }

        $quantityToAdd = isset($data['quantity']) && $data['quantity'] > 0
            ? (int) $data['quantity']
            : 1;

        $existingItem = $this->cartItemRepository->findOneBy([
            'cart' => $cart,
            'product' => $product,
        ]);

        if ($existingItem) {
            $existingItem->setQuantity(
                $existingItem->getQuantity() + $quantityToAdd
            );

            $this->em->flush();

            return [
                'status' => Response::HTTP_OK,
                'item' => $existingItem,
            ];
        }

        $cartItem = new CartItem();
        $cartItem->setCart($cart);
        $cartItem->setTitel($product->getTitel);
        $cartItem->setProduct($product);
        $cartItem->setPrice($product->getPrice());
        $cartItem->setQuantity($quantityToAdd);

        $this->em->persist($cartItem);
        $this->em->flush();

        return [
            'status' => Response::HTTP_CREATED,
            'item' => $cartItem,
        ];

    }
}