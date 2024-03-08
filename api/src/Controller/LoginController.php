<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth')]
class LoginController extends AbstractController
{
    #[Route('/login-user', name: 'api_user_login', methods: ['POST'])]
    public function loginUser(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $user = $userRepository->findOneByEmail($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid Email/Password'], Response::HTTP_UNAUTHORIZED);
        }
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid Email/Password'], Response::HTTP_UNAUTHORIZED);
        }
         if (!$userPasswordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid Email/Password'], Response::HTTP_OK);
        }
        //TODO add TOKEN tws V2 
        $resposeData = [
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'photo' => $user->getPhoto(),
                'roles' => $user->getRoles(),
            ],
            'message' => "User logged in successfully"
        ];

        return new JsonResponse([
            'data' => $resposeData
        ], Response::HTTP_OK);
    }
}
