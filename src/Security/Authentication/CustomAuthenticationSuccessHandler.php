<?php

namespace App\Security\Authentication;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        // Récupère l'utilisateur authentifié
        $user = $token->getUser();

        // Crée le JWT
        $jwt = $this->jwtManager->create($user);

        // Crée la réponse avec les données de l'utilisateur et le token JWT
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'name' => $user->getName(),
            'forename' => $user->getForename(),
            'number' => $user->getNumber(),
            'roles' => $user->getRoles(),
            'token' => $jwt
        ];

        // Retourne la réponse JSON
        return new JsonResponse($userData);
    }
}