<?php

namespace App\Service\DomainService;

use App\Entity\User;
use App\Entity\Address;
use App\Enum\AddressTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AddressUpdateService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    public function updateAddress(array $data, ?User $user, string $id): array
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

        if (isset($data['addressType'])) {
            $address->setAddressType($data['addressType']);
        }
        
        if (isset($data['street'])) {
            $address->setStreet($data['street']);
        }
        
        if (isset($data['postal'])) {
            $address->setPostal($data['postal']);
        }
        
        if (isset($data['city'])) {
            $address->setCity($data['city']);
        }

        $errors = $this->validator->validate($address);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return ['errors' => $errorMessages, 'status' => 400];
        }

        $this->em->flush();

        return [
            'address' => $address, 
            'status' => 200,
            'message' => 'Address updated successfully'
        ];
    }
}