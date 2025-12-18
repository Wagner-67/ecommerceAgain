<?php

namespace App\Controller;

use App\Service\DomainService\AddressCreationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AddressController extends AbstractController
{
    #[Route('/api/address', name: 'app_address', methods: ['POST'])]
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
}