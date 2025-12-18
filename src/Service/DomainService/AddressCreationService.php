<?php

namespace App\Service\DomainService;

use App\Entity\Address;
use App\Entity\User;
use App\Enum\AddressTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddressCreationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {}

    public function addAddress(array $data, ?User $user): array
    {
        if (!$user) {
            return ['errors' => ['User is required'], 'status' => 400];
        }

        $address = new Address();

        $address->setAddressType($data['addressType'] ?? []);
        $address->setStreet($data['street'] ?? '');
        $address->setPostal($data['postal'] ?? '');
        $address->setCity($data['city'] ?? '');
        $address->setUser($user);

        $errors = $this->validator->validate($address);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return ['errors' => $errorMessages, 'status' => 400];
        }

        $this->em->persist($address);
        $this->em->flush();

        return [
            'address' => $address, 
            'status' => 201,
            'message' => 'Address created successfully'
        ];
    }
}