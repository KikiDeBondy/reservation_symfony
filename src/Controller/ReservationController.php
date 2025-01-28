<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Exception\ReservationValidationException;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Services\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{

    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository, SerializerInterface $serializer): Response
    {
        return $this->json([
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

//    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $reservation = new Reservation();
//        $form = $this->createForm(ReservationType::class, $reservation);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($reservation);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('reservation/new.html.twig', [
//            'reservation' => $reservation,
//            'form' => $form,
//        ]);
//    }
    #[Route('/new', name: 'app_reservation_new', methods: ['POST'])]
    public function new(Request $request, ReservationService $reservationService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $reservation = $reservationService->store($data);

            return new JsonResponse($reservation, 201);
        } catch (ReservationValidationException $e) {
            return new JsonResponse([
                'error' => 'Validation failed',
                'form_errors' => $e->getErrors(),
            ], 400);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


//    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
//    public function show(Reservation $reservation): Response
//    {
//        return $this->render('reservation/show.html.twig', [
//            'reservation' => $reservation,
//        ]);
//    }

//    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(ReservationType::class, $reservation);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('reservation/edit.html.twig', [
//            'reservation' => $reservation,
//            'form' => $form,
//        ]);
//    }
//
//    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
//    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($reservation);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
//    }
}
