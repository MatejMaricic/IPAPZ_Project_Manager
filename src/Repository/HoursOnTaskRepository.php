<?php

namespace App\Repository;

use App\Entity\HoursOnTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HoursOnTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method HoursOnTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method HoursOnTask[]    findAll()
 * @method HoursOnTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoursOnTaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HoursOnTask::class);
    }

    // /**
    //  * @return HoursOnTask[] Returns an array of HoursOnTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HoursOnTask
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}