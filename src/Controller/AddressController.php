<?php

namespace App\Controller;

use AddressDeleteService;
use AddressUpdateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DomainService\AddressReadService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\DomainService\AddressCreationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AddressController extends AbstractController
{
    #[Route('/api/address', name: 'app_address_create', methods: ['POST'])]
    public function create(
        Request $request,
        AddressCreationService $addressCreationService,
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        $result = $addressCreationService->addAddress($data, $user);

        $status = $result['status'] ?? 500;
        
        if (isset($result['errors'])) {
            $body = ['errors' => $result['errors']];
        } else {

            $body = [
                'message' => 'Address created successfully',
                'addressId' => $result['address']->getId(),
                'address' => [
                    'id' => $result['address']->getId(),
                    'addressType' => $result['address']->getAddressTypeAsString(),
                    'street' => $result['address']->getStreet(),
                    'postal' => $result['address']->getPostal(),
                    'city' => $result['address']->getCity(),
                    'userId' => $user->getId(),
                ]
            ];
        }

        return new JsonResponse($body, $status);
    }

    #[Route('/api/address', name: 'app_address_read', methods: ['GET'])]
    public function read(
        Request $request,
        AddressReadService $addressReadService,
    ): Jsonresponse
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $result = $addressCreationService->addAddress($user);

        $status = $result['status'] ?? 500;

    }

    #[Route('/api/address/{id}', name: 'app_address_update', methods: ['PATCH'])]
    public function update(
        string $id,
        Request $request,
        AddressUpdateService $addressUpdateService,
    ): JsonResponse
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        $result = $addressCreationService->updateAddress($data, $user, $id);

        $status = $result['status'] ?? 500;

    }

    #[Route('/api/address/{id}', name: 'app_addres_delete', methods: ['DELETE'])]
    public function delete(
        string $id,
        AddressDeleteService $addressDeleteService,
    ): JsonResponse
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        $result = $addressCreationService->deleteAddress($user, $id);

        $status = $result['status'] ?? 500;

    }
}