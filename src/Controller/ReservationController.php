<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation = new Reservation();
        $reservation->setTitle($data['title']);

        try {
            // Convertir le format d'entrée "28-01-2025 19:00:00" en "Y-m-d H:i:s"
            $start = \DateTime::createFromFormat('d-m-Y H:i:s', $data['start']);
            $end = \DateTime::createFromFormat('d-m-Y H:i:s', $data['end']);

            if (!$start || !$end) {
                throw new \Exception('Invalid date format.');
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format. Expected "d-m-Y H:i:s"'], 400);
        }

        $reservation->setStart($start);
        $reservation->setEnd($end);

        $client = $entityManager->getRepository(User::class)->find($data['client_id']);
        $barber = $entityManager->getRepository(User::class)->find($data['barber_id']);

        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], 404);
        }

        if (!$barber) {
            return new JsonResponse(['error' => 'Barber not found'], 404);
        }
        $reservation->setClient($client);
        $reservation->setBarber($barber);
        $form = $this->createForm(ReservationType::class, $reservation, [
            'csrf_protection' => false,  // Désactiver CSRF pour les API
        ]);
        $form->submit($data);
        if ($form->isSubmitted() && !$form->isValid()) {
            // Initialiser un tableau pour stocker les erreurs
            $errors = [];

            // Parcours de tous les champs et récupère leurs erreurs
            foreach ($form->all() as $field) {
                foreach ($field->getErrors() as $error) {
                    // Ajouter chaque message d'erreur dans le tableau
                    $errors[] = $error->getMessage();
                }
            }

            return new JsonResponse([
                'error' => 'Form submission failed',
                'form_errors' => $errors,  // Retourner les erreurs sous forme de tableau

            ], 400);
            
        }


        $entityManager->persist($reservation);
        $entityManager->flush();
        return new JsonResponse($reservation, 201);

    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
