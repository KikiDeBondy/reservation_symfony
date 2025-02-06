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
use Symfony\Component\Serializer\Context\Normalizer\DateTimeNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{

    public function __construct(private readonly ReservationService $reservationService, private readonly SerializerInterface $serializer){}

    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository, SerializerInterface $serializer): Response
    {
        return $this->json([
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Mettre la date en français
            $contextBuilder = (new DateTimeNormalizerContextBuilder())
                ->withFormat('d/m/Y H:i:s');
            // Deserialiser la requête pour avoir un objet Reservation
            $reservation = $this->serializer->deserialize($request->getContent(), Reservation::class, 'json',$contextBuilder->toArray());
            // Récupérer les objets client et coiffeur grâce à leurs id
            $client = $entityManager->getRepository(User::class)->find($request->toArray()['client_id']);
            $barber = $entityManager->getRepository(User::class)->find($request->toArray()['barber_id']);
            $reservation->setClient($client);
            $reservation->setBarber($barber);
            $reservation = $this->reservationService->store($reservation);

            return $this->json($reservation, 201);
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

    #[Route('ByUser/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function reservationByUser(int $id)
    {
        try{
        $reservations = $this->reservationService->reservationByUser($id);
        return $this->json($reservations);
        }catch (\Exception $e){
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des réservations de l\'utilisateur',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/weekly/{start}', name: 'app_reservation_weekly', methods: ['GET'])]
    public function weeklyReservation(\DateTime $start): Response
    {
        try{
            $reservations = $this->reservationService->weeklyReservation($start);
            return $this->json($reservations);
        }catch (\Exception $e){
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des réservations de la semaine',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    #[Route('/delete/{id}/{userId}', name: 'app_reservation_delete', methods: ['DELETE'])]
    public function delete(int $id, int $userId): Response
    {
        try{
            $reservation = $this->reservationService->delete($id, $userId);
            return $this->json($reservation);
        }catch (\Exception $e){
            return new JsonResponse([
                'error' => 'Erreur lors de la suppression de la réservation',
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
