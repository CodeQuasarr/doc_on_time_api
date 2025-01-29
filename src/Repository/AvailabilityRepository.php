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
