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

    public function findAppointmentByUserWithPagination($userId, $page, $limit): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.doctor = :userId')
            ->orWhere('a.patient = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.date', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery()->setHint(Paginator::HINT_ENABLE_DISTINCT, true);
        return new Paginator($query);
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
