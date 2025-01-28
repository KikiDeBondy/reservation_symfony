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

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function store(array $data): Reservation
    {
        $reservation = new Reservation();

        // Validation et parsing des dates
        $start = \DateTime::createFromFormat('d-m-Y H:i:s', $data['start']);
        $end = \DateTime::createFromFormat('d-m-Y H:i:s', $data['end']);

        if (!$start || !$end) {
            throw new ReservationValidationException(['Invalid date format. Expected "d-m-Y H:i:s".']);
        }

        $reservation->setTitle($data['title']);
        $reservation->setStart($start);
        $reservation->setEnd($end);

        // Validation client et barber
        $client = $this->entityManager->getRepository(User::class)->find($data['client_id']);
        $barber = $this->entityManager->getRepository(User::class)->find($data['barber_id']);

        if (!$client || !$barber) {
            throw new EntityNotFoundException('Client or Barber not found.');
        }

        $reservation->setClient($client);
        $reservation->setBarber($barber);

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
}