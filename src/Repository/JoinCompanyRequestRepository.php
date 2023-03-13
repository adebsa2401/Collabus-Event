<?php

namespace App\Repository;

use App\Entity\JoinCompanyRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JoinCompanyRequest>
 *
 * @method JoinCompanyRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method JoinCompanyRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method JoinCompanyRequest[]    findAll()
 * @method JoinCompanyRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoinCompanyRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JoinCompanyRequest::class);
    }

    public function save(JoinCompanyRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JoinCompanyRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findRequests($company = null, $start = null, $end = null, $status = null): array
    {
        $builder = $this->createQueryBuilder('j');

        if ($start && $end) {
            $builder
                ->where('j.startedAt >= :start AND j.startedAt <= :end')
                ->orWhere('j.endedAt >= :start AND j.endedAt <= :end')
                ->orWhere('j.startedAt <= :start AND j.endedAt >= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
        } elseif ($end) {
            $builder
                ->andWhere('j.startedAt <= :end')
                ->setParameter('end', $end);
        } elseif ($start) {
            $builder
                ->andWhere('j.endedAt >= :start')
                ->setParameter('start', $start);
        }

        if ($company) {
            $builder
                ->andWhere('j.requestedTo = :company')
                ->setParameter('company', $company);
        }

        if ($status) {
            $builder
                ->andWhere('j.status = :status')
                ->setParameter('status', $status);
        }

        return $builder
            ->getQuery()
            ->getResult();
    }

    public function findPendingRequests($company = null, $start = null, $end = null): array
    {
        return $this->findRequests($company, $start, $end, JoinCompanyRequest::STATUS_PENDING);
    }

    public function findRelations($company = null, $start = null, $end = null): array
    {
        return $this->findRequests($company, $start, $end, JoinCompanyRequest::STATUS_ACCEPTED);
    }

//    /**
//     * @return JoinCompanyRequest[] Returns an array of JoinCompanyRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JoinCompanyRequest
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
