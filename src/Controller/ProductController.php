<?php

namespace App\Controller;

use App\Service\DomainService\ProductCreationService;
use App\Service\DomainService\ProductDetailService;
use App\Service\DomainService\ProductEditService;
use App\Service\DomainService\ProductDeleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProductController extends AbstractController
{
    #[Route('/api/product', name: 'app_product_create', methods: ['POST'])]
    public function createProduct(
        Request $request,
        ProductCreationService $productCreationService
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        
        $result = $productCreationService->create($data, $user);

        $status = $result['status'] ?? Response::HTTP_CREATED;
        $body = $result['body'] ?? ($result['product'] ?? $result);

        return new JsonResponse($body, $status);
    }

    #[Route('/api/product/{productId}', name: 'app_product_read', methods: ['GET'])]
    public function productDetail(
        string $productId,
        ProductDetailService $productDetailService
    ): JsonResponse {
        $user = $this->getUser();

        $result = $productDetailService->read($productId, $user);

        $status = $result['status'] ?? Response::HTTP_OK;
        $body = $result['body'] ?? $result;

        return new JsonResponse($body, $status);
    }

    #[Route('/api/product/{productId}', name: 'app_product_update', methods: ['PATCH'])]
    public function productEdit(
        Request $request,
        string $productId,
        ProductEditService $productEditService
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true) ?: [];

        $result = $productEditService->edit($productId, $user, $data);

        $status = $result['status'] ?? Response::HTTP_OK;
        $body = $result['body'] ?? $result;

        return new JsonResponse($body, $status);
    }

    #[Route('/api/product/{productId}', name: 'app_product_delete', methods: ['DELETE'])]
    public function deleteProduct(
        string $productId,
        ProductDeleteService $productDeleteService
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->getUser();

        $result = $productDeleteService->delete($productId, $user);

        $status = $result['status'] ?? Response::HTTP_OK;
        $body = $result['body'] ?? $result;

        return new JsonResponse($body, $status);
    }
}
