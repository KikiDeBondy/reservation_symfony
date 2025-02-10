<?php

namespace App\Services;

use App\Entity\Slot;
use App\Entity\User;
use App\Repository\SlotRepository;
use Doctrine\ORM\EntityManagerInterface;

class SlotService
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly SlotRepository $slotRepository)
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

    public function weeklySlotUnreserve(int $id): array
    {
        return $this->slotRepository->weeklySlotUnreserve($id);
    }



}