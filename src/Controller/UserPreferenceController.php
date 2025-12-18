<?php

namespace App\Controller;

use App\Service\DomainService\UserPreferenceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserPreferenceController extends AbstractController
{
    #[Route('/api/user/preference', name: 'app_user_preference', methods: ['POST'])]
    public function create(
        Request $request,
        UserPreferenceService $userPreferenceService
    ): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        
        if ($data === null) {
            return new JsonResponse(
                ['error' => 'Invalid JSON data'], 
                Response::HTTP_BAD_REQUEST
            );
        }

        try {

            $result = $userPreferenceService->createWithCookieResponse($data);

            $response = new JsonResponse(
                ['status' => 'success', 'darkMode' => $result['darkMode']], 
                Response::HTTP_CREATED
            );
            
            $response->headers->setCookie($result['cookie']);
            
            return $response;
            
        } catch (AccessDeniedException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()], 
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}