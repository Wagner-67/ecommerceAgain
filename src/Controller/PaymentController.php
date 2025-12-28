<?php

namespace App\Controller;

use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    #[Route('/api/payment/create-intent/{orderId}', name: 'payment_create_intent', methods: ['POST'])]
    public function createPaymentIntent(int $orderId): JsonResponse
    {
        try {
            $result = $this->paymentService->createPaymentIntent($orderId);
            return new JsonResponse($result);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/payment/confirm', name: 'payment_confirm', methods: ['POST'])]
    public function confirmPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $paymentIntentId = $data['payment_intent_id'] ?? null;

        if (!$paymentIntentId) {
            return new JsonResponse(['error' => 'Payment intent ID required'], 400);
        }

        try {
            $this->paymentService->confirmPayment($paymentIntentId);
            return new JsonResponse(['status' => 'confirmed']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}