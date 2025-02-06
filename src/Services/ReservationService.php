<?php

namespace App\Services;

use App\Entity\Reservation;
use App\Entity\User;
use App\Exception\ReservationValidationException;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationService
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, private readonly ReservationRepository $reservationRepository)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function store(Reservation $reservation): Reservation
    {

        // Validation des données de la réservation
        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new ReservationValidationException($errorMessages);
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }

    public function reservationByUser(int $id): array
    {
        return $this->reservationRepository->findByUser($id);
    }
    public function weeklyReservation(\DateTime $start): array
    {
        return $this->reservationRepository->weeklyReservation($start);
    }

    public function delete(int $id, int $userId): Reservation
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            throw new EntityNotFoundException('La réservation n\'existe pas');
        }
        if($reservation->getClient()->getId() !== $userId){
            throw new EntityNotFoundException('Vous n\'êtes pas autorisé à supprimer cette réservation');
        }
        // Si la réservation est déjà passée, on ne peut pas la supprimer
        if($reservation->getStart() < new \DateTime()){
            throw new EntityNotFoundException('Vous ne pouvez pas supprimer une réservation passée');
        }
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();
        return $reservation;
    }
}