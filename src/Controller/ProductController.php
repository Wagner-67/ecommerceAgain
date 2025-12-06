<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\DomainService\ProductCreationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    #[Route('api/product', name: 'app_product_create', methods: ['POST'])]
    public function createProduct(
        Request $request,
        EntityManagerInterface $em,
        ProductCreationService $productCreationService
    ): JsonResponse {
        
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        $result = $productCreationService->register($data);

        return new JsonResponse($result, Response::HTTP_CREATED);

    }
}
