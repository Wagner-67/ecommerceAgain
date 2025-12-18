<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Listener\DomainListener\UserDeleteService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DateTimeImmutable;
use DateTimeZone;

final class EmailController extends AbstractController
{
    #[Route('/api/user/verify/{verifyToken}', name: 'app_verify_email', methods: ['GET'])]
    public function verifyEmail(
        string $verifyToken,
        EntityManagerInterface $em,
    ): JsonResponse {

        if(!$verifyToken) {
            return new JsonResponse([
                'success' => false,
                'errors' => 'Verification token is required.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $em->getRepository(User::class)->findOneBy(['verifiedToken' => $verifyToken]);

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'errors' => 'Invalid verification token.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->setIsVerified(true);
        $user->setVerifiedToken(null);
        $user->setVerifiedAt(new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin')));
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Email successfully verified.'], Response::HTTP_OK);

    }

    #[Route('/api/user/delete/{deleteToken}', name: 'app_account_deleted', methods: ['GET'])]
    public function accountDeleted(
        string $deleteToken,
        EntityManagerInterface $em,
        UserDeleteService $userDeleteService,
    ): JsonResponse {

        $user = $this->getUser();

        $result = $userDeleteService->deleteUserByToken($deleteToken, $user);

         return new JsonResponse($result['body'] ?? $result, $result['status'] ?? Response::HTTP_OK);
    }

    #[Route('/api/user/password{resetToken}', name: 'api_password_reset_confirm', methods: ['PATCH'])]
    public function passwordResetConfirm(
        string $resetToken,
        Request $request,
        EntityManagerInterface $em,
        PasswordResetService $PasswordResetService,
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        $result = $PasswordResetService->setPassword($data);

        return new JsonResponse($result, $result['status'] ?? Response::HTTP_OK);
    }
}
