<?php

namespace App\Service\DomainService;

use App\Entity\User;
use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddressDeleteService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    public function deleteAddress(?User $user, string $id): array
    {

        if (!$id) {
            throw new AccessDeniedHttpException('Address Id is required');
        }

        if (!$user) {
            return ['errors' => ['User is required'], 'status' => 400];
        }

        $address = $this->em->getRepository(Address::class)->findOneBy([
            'id' => $id,
            'user' => $user 
        ]);

        if (!$address) {
            return ['error' => 'Address not found or access denied', 'status' => 404];
        }

        $this->em->remove($address);
        $this->em->flush();

        return [
            'status' => 200,
            'message' => 'Address deleted successfully'
        ];
    }

}