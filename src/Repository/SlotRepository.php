<?php

namespace App\Repository;

use App\Entity\Slot;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Slot>
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    //Retourner une semaine des slots non réservé d'un coiffeur donné
    public function weeklySlotUnreserve(int $id, int $page, int $limit = 7): array
    {
        $today = new \DateTime();
        $start = (clone $today)->modify("+".($page * $limit)." days");
        $end = (clone $start)->modify("+".($limit - 1)." days");
        $hour = (clone $today)->modify("+1hour");

        // Initialisation : tableau contenant tous les jours demandés (même vides)
        $days = [];
        for ($i = 0; $i < $limit; $i++) {
            $day = (clone $start)->modify("+$i days")->format('Y-m-d');
            $days[$day] = []; // Initialiser chaque jour avec un tableau vide
        }

        // Récupération des créneaux disponibles
        $results = $this->createQueryBuilder('s')
            ->andWhere('s.barber_id = :id')
            ->andWhere('s.date BETWEEN :start AND :end')
            ->andWhere('s.is_reserved = 0')
            ->andWhere('(s.date = :today AND s.start > :hour) OR (s.date > :today)')
            ->setParameter('id', $id)
            ->setParameter('today', $today->format('Y-m-d'))
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->setParameter('hour', $hour->format('H:i:s'))
            ->getQuery()
            ->getResult();

        // Organisation des créneaux par jour
        foreach ($results as $slot) {
            $slotDate = $slot->getDate()->format('Y-m-d'); // Adapter en fonction de ton entité
            $days[$slotDate][] = $slot;
        }

        // Retourner le tableau contenant **tous les jours**, même vides
        // Permet d'alléger le frontend
        return $days;
    }



    //Retourner une semaine des slots d'un coiffeur donné
//    public function weeklySlot(int $id): array
//    {
//        $start = new \DateTime('monday this week');
//        $end = new \DateTime('sunday this week');
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.barber_id = :id')
//            ->andWhere('s.date BETWEEN :start AND :end')
//            ->setParameter('id', $id)
//            ->setParameter('start', $start)
//            ->setParameter('end', $end)
//            ->getQuery()
//            ->getResult();
//    }
    public function weeklySlot(int $barberId, int $page, int $limit = 7): array
    {
        $today = new \DateTime();
        $startDate = (clone $today)->modify("+".($page * $limit)." days");
        $endDate = (clone $startDate)->modify("+".($limit - 1)." days");

        $data = $this->createQueryBuilder('s')
            ->andWhere('s.barber_id = :barberId')
            ->andWhere('s.date BETWEEN :startDate AND :endDate')
            ->setParameter('barberId', $barberId)
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00'))
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59'))
            ->orderBy('s.date', 'ASC')
            ->addOrderBy('s.start', 'ASC')
            ->getQuery()
            ->getResult();
        return $data;
    }


    //Retourner une valeur qui possède la date donné
    public function slotOfBarberExist(int $id,DateTime $date): bool{
        $data = $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->andWhere('s.barber_id = :id')
            ->andWhere('s.date = :date')
            ->setParameter('id', $id)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
        return $data > 0;
    }

    public function slotSlotOfBarberBetweenDate(int $barberId, DateTime $start, DateTime $end): array{
        return $this->createQueryBuilder('s')
            ->andWhere('s.barber_id = :id')
            ->andWhere('s.date BETWEEN :start AND :end')
            ->setParameter('id', $barberId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Slot[] Returns an array of Slot objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Slot
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
