<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\Availability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }



    public function save(Availability $availability): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($availability);
        $entityManager->flush();
    }

    public function findAppointmentByUserWithPagination($userId, $page, $limit, $date): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.doctor = :userId')
            ->orWhere('a.patient = :userId')
            ->andWhere('a.date = :date')
            ->setParameter('userId', $userId)
            ->setParameter('date', $date)
            ->orderBy('a.date', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery()->setHint(Paginator::HINT_ENABLE_DISTINCT, true);
        return new Paginator($query);
    }

    public function findTodayAppointHours($doctorId, $date): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a.hour')
            ->where('a.doctor = :doctorId')
            ->andWhere('a.date = :date')
            ->setParameter('doctorId', $doctorId)
            ->setParameter('date', $date)
            ->orderBy('a.hour', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function findAppointmentsDatesAndHoursForWeek($doctorId): array
    {
        // Convertir la date fournie en DateTime pour manipuler les jours de la semaine
        $currentDate = new \DateTime();

        // Trouver le lundi de la semaine actuelle
        $startOfWeek = (clone $currentDate)->modify('this week')->modify('monday');

        // Trouver le samedi de la semaine actuelle
        $endOfWeek = (clone $startOfWeek)->modify('saturday');


        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a.date AS date, a.hour AS hour')
            ->where('a.doctor = :doctorId')
            ->andWhere('a.date BETWEEN :startOfWeek AND :endOfWeek')
            ->setParameter('doctorId', $doctorId)
            ->setParameter('startOfWeek', $startOfWeek->format('Y-m-d'))
            ->setParameter('endOfWeek', $endOfWeek->format('Y-m-d'))
            ->orderBy('a.date', 'ASC')
            ->addOrderBy('a.hour', 'ASC')
        ;
        return $queryBuilder->getQuery()->getResult();
    }

    //    /**
    //     * @return Appointment[] Returns an array of Appointment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Appointment
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
