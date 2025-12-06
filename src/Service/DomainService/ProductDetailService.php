<?php

namespace App\Service\DomainService;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ProductDetailService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    public function read(string $productId ,?User $user): array
    {

        if(!$user) {
            throw new AccessDeniedException('Authentication required');
        }

        if(!$this->security->isGranted('ROLE_USER', $user)) {
            throw new AccessDeniedException('access required');
        }

        if(!$productId) {
            return ['error' => 'Delete token is required', 'status' => 400];
        }

        $product = $this->em->getRepository(Product::class)
            ->findOneBy(['productId'=> $productId]);

        if(!$product) {
            return ['error' => 'Product not found', 'status' => 404];
        }

        $adminReturn = [
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'stock' => $product->getStock(),
            'is_active' => $product->isActive(),
            'slug' => $product->getSlug(),
            'updated_at' => $product->getUpdatedAt()->format('Y-m-d H:i:s'), 
            'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s'),
            'created_by' => $product->getCreatedBy() ? $product->getCreatedBy()->getEmail() : null,
            'product_id' => $product->getProductId(),
        ];

        $userReturn = [
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'stock' => $product->getStock(),
        ];

        if ($this->security->isGranted('ROLE_ADMIN', $user)) {
            return $adminReturn;
        }

        return $userReturn;
    }
}