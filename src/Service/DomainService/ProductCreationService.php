<?php

namespace App\Service\DomainService;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductCreationService
{

    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private Security $security,
    ) {}

    public function create(array $data, ?User $user): array
    {

        if (!$user) {
            throw new AccessDeniedException('Authentication required');
        }
        
        if (!$this->security->isGranted('ROLE_ADMIN', $user)) {
            throw new AccessDeniedException('Admin access required');
        }

        $product = new Product();
        $product->setTitel($data['title'] ?? '');
        $product->setDescription($data['description'] ?? '');
        $product->setPrice((string) ($data['price'] ?? '0'));
        $product->setStock((int) ($data['stock'] ?? 0));
        $product->setIsActive((bool) ($data['isActive'] ?? false));
        $product->setCreatedBy($user);
        
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return ['errors' => $errorMessages, 'status' => 400];
        }

        $this->em->persist($product);
        $this->em->flush();
        
        return ['product' => $product, 'status' => 201];
    }

}