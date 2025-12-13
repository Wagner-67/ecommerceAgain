<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\DomainService\CartItemService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\DomainService\CartItemUpdateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CartController extends AbstractController
{
    #[Route('/api/cart-items/{productId}', name: 'app_cart-item_create', methods: ['POST'])]
    public function addCarItem(
        string $productId,
        Request $request,
        EntityManagerInterface $em,
        CartItemService $cartItemService,
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        $result = $cartItemService->addCartItem($data, $user, $productId);

        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);
    }

    #[Route('/api/cart-items/{cartItemId}', name: 'app_cart_item_update', methods: ['PATCH'])]
    public function updateCartItem(
        string $cartItemId,
        Request $request,
        EntityManagerInterface $em,
        CartItemUpdateService $cartItemUpdateService,
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        
        $data = json_decode($request->getContent(), true);

        $result = $cartItemUpdateService->updateCartItem($data, $user, $cartItemId);

        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);
    }

    #[Route('/api/cart', name: 'app_cart_read', methods: ['GET'])]
    public function readCart(
        CartListService $cartListService,
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $result = $cartListService->listCart($user);
        
        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);
    }
}
