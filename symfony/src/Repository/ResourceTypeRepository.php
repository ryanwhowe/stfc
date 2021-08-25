<?php

namespace App\Repository;

use App\Entity\ResourceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResourceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceType[]    findAll()
 * @method ResourceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceType::class);
    }

    // /**
    //  * @return ResourceType[] Returns an array of ResourceType objects
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
    public function findOneBySomeField($value): ?ResourceType
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
