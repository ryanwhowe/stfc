<?php

namespace App\Repository;

use App\Entity\ResourceDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResourceDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceDetail[]    findAll()
 * @method ResourceDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceDetail::class);
    }

    // /**
    //  * @return ResourceDetail[] Returns an array of ResourceDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResourceDetail
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
