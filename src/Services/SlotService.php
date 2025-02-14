<?php

namespace App\Services;

use App\Entity\Slot;
use App\Entity\User;
use App\Repository\SlotRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

class SlotService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SlotRepository $slotRepository,
        private readonly UserRepository $userRepository,
    )
    {
    }


    public function update(int $id, $data): void
    {
        // Récupérer le slot à mettre à jour
        $slot = $this->slotRepository->find($id);
        // Mettre à jour les données du slot
        $slot->setIsReserved($data['is_reserved']);
        //Persister les modifications
        $this->entityManager->persist($slot);
        $this->entityManager->flush();
    }

    public function absent(int $id, DateTime $startDate, DateTime $endDate){
        try {
            $slots = $this->slotRepository->slotSlotOfBarberBetweenDate($id, $startDate, $endDate);

            foreach($slots as $slot){
                $slot->setIsReserved(true);
                $this->entityManager->persist($slot);
                $this->entityManager->flush();
            }
            return $slots;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function weeklySlotUnreserve(int $id): array
    {
        // Récupérer les données de la base de données
        $data = $this->slotRepository->weeklySlotUnreserve($id);
        return $data;
    }
    public function weeklySlot(int $id): array
    {
        // Récupérer les données de la base de données
        $data = $this->slotRepository->weeklySlot($id);
        return $data;
    }
    public function slotOfBarberExist(int $barberId, DateTime $date): bool{
        return $this->slotRepository->slotOfBarberExist($barberId, $date);
    }

    public function generateSlot(int $barberId, DateTime $startDate, DateTime $endDate): Slot{
        $barber = $this->userRepository->find($barberId);
        if (!$barber) {
            throw new \Exception("Barber introuvable");
        }

        $slotOfBarberExist = $this->slotOfBarberExist($barberId, $startDate);
        if ($slotOfBarberExist) {
            throw new \Exception('Une des dates est déjà ajouter à vos créneaux');
        }


        // Génération des créneaux de 30 minutes de 9 à 18h, pour chaque jour entre $startDate et $endDate
        $interval = new \DateInterval('PT30M');
        $start = new \DateTime('09:00:00');
        $end = new \DateTime('18:00:00');
        $date = clone $startDate;
        while ($date <= $endDate) {
            $current = clone $start;
            while ($current < $end) {
                $slot = new Slot();
                $slot->setBarberId($barber);
                $slot->setDate(clone $date);
                $slot->setStart(clone $current);
                $current->add($interval);
                $slot->setEnd(clone $current);
                $slot->setIsReserved(false);
                $this->entityManager->persist($slot);
                $this->entityManager->flush();
            }
            $date->modify('+1 day');
        }
        return $slot;
    }

}