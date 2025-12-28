<?php

namespace App\Service\DomainService;

use App\Entity\Cart;
use App\Entity\Orders;
use App\Entity\OrderItems;
use App\Entity\User;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class OrderSetService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    public function create(array $data, ?User $user): array
    {
        if (!$user) {
            return [
                'status' => 401,
                'body' => ['error' => 'User not authenticated']
            ];
        }

        $cart = $user->getCart();
        if (!$cart || $cart->getCartItems()->isEmpty()) {
            return [
                'status' => 400,
                'body' => ['error' => 'Cart is empty']
            ];
        }

        $order = new Orders();
        $order->setOrderStatus(OrderStatus::PENDING_PAYMENT);
        $order->setDeliveryAddress($data['deliveryAddress'] ?? []);
        $order->setBillingAddress($data['billingAddress'] ?? []);
        $order->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin')));
        $order->setExpiresAt(new \DateTimeImmutable('+30 minutes', new \DateTimeZone('Europe/Berlin')));
        $order->setIsFinished(false);

        foreach ($cart->getCartItems() as $cartItem) {
            $orderItem = new OrderItems();
            $orderItem->setProductName($cartItem->getProduct()->getTitel());
            $orderItem->setProductId($cartItem->getProduct()->getId());
            $orderItem->setUnitPrice((float) $cartItem->getPrice());
            $orderItem->setQuantity((float) $cartItem->getQuantity());
            $orderItem->setTotalPrice((float) $cartItem->getPrice() * (float) $cartItem->getQuantity());

            $order->addOrderItem($orderItem);
        }

        $this->em->persist($order);
        $this->em->flush();

        return [
            'status' => 201,
            'order' => $order
        ];
    }

}