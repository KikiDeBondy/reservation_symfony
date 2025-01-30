<?php

namespace App\Services;

use App\Entity\Reservation;
use App\Entity\User;
use App\Exception\RegisterValidationException;
use App\Exception\ReservationValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterService
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, private UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function store(User $user): User
    {
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        // Validation des données de la réservation
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new RegisterValidationException($errorMessages);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}