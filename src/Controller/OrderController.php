<?php

namespace App\Controller;

use App\Service\DomainService\OrderSetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/api/order', name: 'app_order_create', methods: ['POST'])]
    public function create(
        Request $request,
        OrderSetService $orderSetService,
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        $result = $orderSetService->create($data, $user);

        $status = $result['status'] ?? Response::HTTP_CREATED;
        $body = $result['body'] ?? ($result['order'] ?? $result);

        return new JsonResponse($body, $status);

    }
}
