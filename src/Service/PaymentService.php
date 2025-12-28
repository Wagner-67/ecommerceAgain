<?php

namespace App\Service;

use App\Entity\Orders;
use App\Entity\Payment;
use App\Enum\OrderStatus;
use App\Message\GenerateOrderPdfMessage;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Component\Messenger\MessageBusInterface;

class PaymentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private string $stripeSecretKey,
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createPaymentIntent(int $orderId): array
    {
        $order = $this->entityManager->getRepository(Orders::class)->find($orderId);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        if ($order->getOrderStatus() !== OrderStatus::PENDING_PAYMENT) {
            throw new \Exception('Order is not pending payment');
        }

        $totalAmount = 0;
        foreach ($order->getOrderItems() as $item) {
            $totalAmount += $item->getTotalPrice();
        }

        // Assuming currency is EUR, adjust as needed
        $currency = 'eur';

        $paymentIntent = PaymentIntent::create([
            'amount' => (int)($totalAmount * 100), // Stripe expects cents
            'currency' => $currency,
            'metadata' => [
                'order_id' => $orderId,
            ],
        ]);

        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setStripePaymentIntentId($paymentIntent->id);
        $payment->setAmount($totalAmount);
        $payment->setCurrency($currency);
        $payment->setStatus('pending');

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return [
            'client_secret' => $paymentIntent->client_secret,
            'payment_id' => $payment->getId(),
        ];
    }

    public function confirmPayment(string $paymentIntentId): void
    {
        $payment = $this->entityManager->getRepository(Payment::class)->findOneBy(['stripePaymentIntentId' => $paymentIntentId]);

        if (!$payment) {
            throw new \Exception('Payment not found');
        }

        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

        if ($paymentIntent->status === 'succeeded') {
            $payment->setStatus('succeeded');
            $order = $payment->getOrder();
            $order->setOrderStatus(OrderStatus::PAID);

            $this->entityManager->flush();

            // Dispatch async PDF generation
            $this->messageBus->dispatch(new GenerateOrderPdfMessage($order->getId()));
        } else {
            $payment->setStatus($paymentIntent->status);
            $this->entityManager->flush();
        }
    }
}