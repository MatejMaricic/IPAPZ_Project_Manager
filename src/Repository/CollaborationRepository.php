<?php

namespace App\Repository;

use App\Entity\Collaboration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Collaboration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collaboration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collaboration[]    findAll()
 * @method Collaboration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollaborationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Collaboration::class);
    }


    /*
    public function findOneBySomeField($value): ?Collaboration
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
