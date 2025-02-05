<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findByUser(int $id){
        return $this->createQueryBuilder('r')
            ->andWhere('r.client = :id')
            ->setParameter('id', $id)
            ->orderBy('r.start', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function weeklyReservation(\DateTime $start){
        // Retourner les réservations de la semaine (7 jours) à partir de la date $start
        $end = clone $start;
        $end->modify('+7 days');
        return $this->createQueryBuilder('r')
            ->andWhere('r.start >= :start')
            ->andWhere('r.start < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('r.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
