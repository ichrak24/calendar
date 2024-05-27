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

    public function findReservationsByWeek(\DateTimeInterface $startOfWeek, \DateTimeInterface $endOfWeek): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.startDate >= :startOfWeek')
            ->andWhere('r.endDate <= :endOfWeek')
            ->setParameter('startOfWeek', $startOfWeek->format('Y-m-d H:i:s'))
            ->setParameter('endOfWeek', $endOfWeek->format('Y-m-d H:i:s'))
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