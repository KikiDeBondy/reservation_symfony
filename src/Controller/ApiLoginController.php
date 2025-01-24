<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordEncoder, UserRepository $userRepository): JsonResponse
    {
        // Récupérer les données envoyées par la requête (username, password)
        $data = json_decode($request->getContent(), true);

        // Vérifier si 'username' (email) et 'password' sont fournis
        if (empty($data['username']) || empty($data['password'])) {
            return new JsonResponse(['error' => 'Username and password are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupérer l'utilisateur par l'email
        $user = $userRepository->findOneBy(['email' => $data['username']]);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Vérifier si le mot de passe est correct
        if (!$passwordEncoder->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid password.'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'name' => $user->getName(),
            'forename' => $user->getForename(),
            'number' => $user->getNumber(),
            'roles' => $user->getRoles(),
        ];

        return new JsonResponse($userData);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout()
    {

    }

}
