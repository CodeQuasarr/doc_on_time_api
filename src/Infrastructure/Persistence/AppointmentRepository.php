<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\AppointmentRepositoryInterface;
use App\Entity\Appointment;
use App\Entity\Availability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository implements  AppointmentRepositoryInterface
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

    public function findAppointmentByUserWithPagination(int $userId, $page, $limit, $date): Paginator
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

    public function findAllAppointments(): array
    {
        return $this->findAll();
    }

    public function findDoctorAppointmentHoursForToday(int $doctorId, string $date): array
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
    public function findDoctorWeeklyAppointments(int $doctorId): array
    {
        // Convertir la date d\'aujourd'hui en DateTime pour manipuler les jours de la semaine
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
}
