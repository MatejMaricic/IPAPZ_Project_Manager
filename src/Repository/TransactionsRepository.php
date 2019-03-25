<?php

namespace App\Repository;

use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transactions[]    findAll()
 * @method Transactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transactions::class);
    }

     /**
      * @return Transactions[] Returns an array of Transactions objects
      */

    public function findByBuyerEmail($email)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.buyerEmail = :email')
            ->setParameter('email', $email)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }
}
