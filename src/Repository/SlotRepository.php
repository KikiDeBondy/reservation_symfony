<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Slot>
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    //Retourner une semaine des slots d'un coiffeur donné
    public function weeklySlotUnreserve(int $id): array
    {
        $start = new \DateTime('monday this week');
        $end = new \DateTime('sunday this week');
        return $this->createQueryBuilder('s')
            ->andWhere('s.barber_id = :id')
            ->andWhere('s.date BETWEEN :start AND :end')
            ->andWhere('s.is_reserved = 0')
            ->setParameter('id', $id)
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
