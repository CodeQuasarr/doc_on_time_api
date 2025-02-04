<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\AvailabilityRepositoryInterface;
use App\Entity\Availability;
use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Availability>
 */
class AvailabilityRepository extends ServiceEntityRepository implements AvailabilityRepositoryInterface
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

    public function findAllAvailabilities(): array
    {
        return $this->findAll();
    }

    public function findAvailabilitiesByDoctorWithPagination(int $doctorInfo, int $page, int $limit): Paginator
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

    public function findDoctorAvailabilityForNextTwoDay(int $doctorInfo): array
    {
        $today = (new DateTime('today'))->format('Y-m-d');
        $afterTomorrow = (new DateTime('+2 days'))->format('Y-m-d');


        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.doctor_info = :doctorInfoId')
            ->andWhere('a.date >= :today')
            ->andWhere('a.date < :afterTomorrow')
            ->setParameter('doctorInfoId', $doctorInfo)
            ->setParameter('today', $today)
            ->setParameter('afterTomorrow', $afterTomorrow)
            ->orderBy('a.date', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function findDoctorWeeklyAvailabilitiesAndSlots(int $doctorInfo, string $currentDate): array
    {
        $date = new DateTime($currentDate, new DateTimeZone('Europe/Paris'));


        // Trouver le lundi de la semaine actuelle
        $startOfWeek = (clone $date)->modify('this week')->modify('monday');

        // Trouver le samedi de la semaine actuelle
        $endOfWeek = (clone $startOfWeek)->modify('saturday');
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a.id, a.date, a.slots')
            ->where('a.doctor_info = :doctorInfoId')
            ->andWhere('a.date >= :startOfWeek')
            ->andWhere('a.date <= :endOfWeek')
            ->setParameter('doctorInfoId', $doctorInfo)
            ->setParameter('startOfWeek', $startOfWeek->format('Y-m-d'))
            ->setParameter('endOfWeek', $endOfWeek->format('Y-m-d'))
            ->orderBy('a.date', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }
}
