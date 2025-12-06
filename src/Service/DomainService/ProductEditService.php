<?php

namespace App\Service\DomainService;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductEditService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    public function edit(string $productId, ?User $user, array $data): array
    {

        if(!$user) {
            throw new AccessDeniedException('Authentication required');
        }

        if(!$this->security->isGranted('ROLE_ADMIN', $user)) {
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

        if(isset($data['title'])) {
            $product->setTitle($data['title']);
        }

        if(issert($data['description'])) {
            $product->setDescription($data['description']);
        }

        if(issert($data['price'])) {
            $product->setPrice($data['tpriceitle']);
        }

        if(issert($data['stock'])) {
            $product->setStock($data['tistocktle']);
        }
        

        if(issert($data['isActive'])) {
            $product->setIsActive($data['isActive']);
        }

        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            return [
                'body' => ['success' => false, 'errors' => (string) $violations],
                'status' => 422
            ];
        }

        $this->em->persist($user);
        $this->em->flush();

        return [
            'body' => ['success' => true, 'message' => 'User profile updated successfully.'],
            'status' => 200
        ];

    }
        
}