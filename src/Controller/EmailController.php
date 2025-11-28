<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class EmailController extends AbstractController
{
    #[Route('/api/verify/{verifyToken}', name: 'api_verify_email', methods: ['GET'])]
    public function verifyEmail(
        string $verifyToken,
        EntityManagerInterface $em
    ): JsonResponse {

        if(!$verifyToken) {
            return new JsonResponse([
                'success' => false,
                'errors' => 'Verification token is required.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $em->getRepository(User::class)->findOneBy(['verifyToken' => $verifyToken]);

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'errors' => 'Invalid verification token.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->setIsVerified(true);
        $user->setVerifyToken(null);
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Email successfully verified.'], Response::HTTP_OK);

    }

}
