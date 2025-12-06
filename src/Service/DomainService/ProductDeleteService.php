<?php

namespace App\Service\DomainService;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductDeleteService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function delete(string $productId, ?User $user): array
    {
        if (!$user) {
            throw new AccessDeniedException('Authentication required');
        }

        if (!$this->security->isGranted('ROLE_ADMIN', $user)) {
            throw new AccessDeniedException('Admin access required');
        }

        if (!$productId) {
            return ['error' => 'Product id is required', 'status' => 400];
        }

        $product = $this->em->getRepository(Product::class)
            ->findOneBy(['productId' => $productId]);

        if (!$product) {
            return ['error' => 'Product not found', 'status' => 404];
        }

        $this->em->remove($product);
        $this->em->flush();

        return [
            'body' => ['success' => true, 'message' => 'Product deleted successfully.'],
            'status' => 200,
        ];
    }
}
