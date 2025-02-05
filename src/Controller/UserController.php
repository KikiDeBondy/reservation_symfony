<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//#[Route('/api/user')]
final class UserController extends AbstractController
{
    #[Route('/api/user',name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->json([
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('api/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->json($user);
    }

//
//    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(UserType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('user/edit.html.twig', [
//            'user' => $user,
//            'form' => $form,
//        ]);
//    }
//
//    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
//    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($user);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
//    }
}
