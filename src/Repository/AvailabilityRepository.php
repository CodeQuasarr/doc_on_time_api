<?php

namespace App\Repository;

use App\Entity\Availability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Availability>
 */
class AvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Availability::class);
    }

    public function save(Availability $availability): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($availability);
        $entityManager->flush();
    }

    public function findAvailabilitiesByDoctorWithPagination($doctorInfo, int $page, int $limit): Paginator
    {

        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.doctor_info = :doctorInfo')
            ->setParameter('doctorInfo', $doctorInfo)
            ->orderBy('a.date', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery()->setHint(Paginator::HINT_ENABLE_DISTINCT, true);
        return new Paginator($query);
    }

    public function findNextTwoDaysAvailabilityByDoctor($doctorInfoId): array
    {

        // Conversion des dates en string au format 'Y-m-d'
        $today = (new \DateTime('today'))->format('Y-m-d');
        $afterTomorrow = (new \DateTime('+2 days'))->format('Y-m-d');


        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.doctor_info = :doctorInfoId')
            ->andWhere('a.date >= :today')
            ->andWhere('a.date < :afterTomorrow')
            ->setParameter('doctorInfoId', $doctorInfoId)
            ->setParameter('today', $today)
            ->setParameter('afterTomorrow', $afterTomorrow)
            ->orderBy('a.date', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function findAvailabilitiesDatesAndSlotsForWeek($doctorId, $currentDate) {
        // Convertir la date fournie en DateTime pour manipuler les jours de la semaine
        // Convertir la date actuelle en DateTime pour manipuler les jours de la semaine
        $currentDate = new \DateTime($currentDate, new \DateTimeZone('Europe/Paris'));


        // Trouver le lundi de la semaine actuelle
        $startOfWeek = (clone $currentDate)->modify('this week')->modify('monday');

        // Trouver le samedi de la semaine actuelle
        $endOfWeek = (clone $startOfWeek)->modify('saturday');
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a.id, a.date, a.slots')
            ->where('a.doctor_info = :doctorInfoId')
            ->andWhere('a.date >= :startOfWeek')
            ->andWhere('a.date <= :endOfWeek')
            ->setParameter('doctorInfoId', $doctorId)
            ->setParameter('startOfWeek', $startOfWeek->format('Y-m-d'))
            ->setParameter('endOfWeek', $endOfWeek->format('Y-m-d'))
            ->orderBy('a.date', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }
    //    /**
    //     * @return Availability[] Returns an array of Availability objects
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

    //    public function findOneBySomeField($value): ?Availability
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
