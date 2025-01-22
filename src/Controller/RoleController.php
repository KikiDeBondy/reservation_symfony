<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class RoleController extends AbstractController
{
    private $roleRepository;
    private $serializer;

    // Injection des dépendances
    public function __construct(RoleRepository $roleRepository, SerializerInterface $serializer)
    {
        $this->roleRepository = $roleRepository;
        $this->serializer = $serializer;
    }

    #[Route('/role', name: 'app_role', methods: ['GET'])]
    public function index(): Response
    {
        // Récupérer tous les rôles
        $roles = $this->roleRepository->findAll();

        // Retourner directement les données sous forme de tableau, sans les sérialiser
        return $this->json([
            'code' => 200,
            'result' => $roles
        ]);
    }
    #[Route('role/create', name: 'role_create', methods: ['POST'])]
    public function create(Request $request){
        try{
            $role = new Role();
            $role->setLabel($request->get('role'));
            $this->roleRepository->create($role);
            return $this->json([
                'code' => 200,
                'message' => 'Role créer avec succes'
            ]);
        }catch (\Exception $e){
            return $this->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
