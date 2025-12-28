<?php

namespace App\Service\DomainService;

use App\Entity\User;
use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AddressReadService
{

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
    ) {}

    public function addAddress(?User $user): array
    {

        $userAddress = $user->getAddresses();
        $addressRepo = $this->em->getRepository(Address::class)->findBy(['user' => $userAddress]);

        $address = [];
        foreach ($addressRepo as $addressRepo) {
            $address[] = [
                'id' => $addressRepo->getId(),
                'address_type' => $addressRepo->getAddressType(),
                'street' => $addressRepo->getStreet(),
                'postal' => $addressRepo->getPostal(),
                'city' => $addressRepo->getCity(),
                'createdAt' => $addressRepo->getCreatetAt()
            ];
        }

        return [
            'address' => $address,
            'status' => Response::HTTP_OK
        ];
    }
}