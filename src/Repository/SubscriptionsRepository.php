<?php

namespace App\Repository;

use App\Entity\Subscriptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Subscriptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscriptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscriptions[]    findAll()
 * @method Subscriptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Subscriptions::class);
    }

     /**
      * @return Subscriptions[] Returns an array of Subscriptions objects
      */

    public function findByTask($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.taskId = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $task_id
     * @param $user_email
     * @return Subscriptions
     */

    public function checkSubscriber($task_id, $user_email)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.taskId = :task_id')
            ->andWhere('s.userEmail = :user_email')
            ->setParameter('task_id', $task_id)
            ->setParameter('user_email', $user_email)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Subscriptions
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
