<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllDevelopers($id)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.roles LIKE :val')
            ->andWhere('u.addedBy = :id')
            ->setParameter('id', $id)
            ->setParameter('val', "%ROLE_USER%")
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10);
    }

    public function findAllDevelopersArray()
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.roles LIKE :val')
            ->setParameter('val', "%ROLE_USER%")
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findAllManagersArray()
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.roles LIKE :val')
            ->setParameter('val', "%ROLE_MANAGER%")
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findAllDevelopersForManagerArray($id)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.roles LIKE :val')
            ->andWhere('u.addedBy = :id')
            ->setParameter('val', "%ROLE_USER%")
            ->setParameter('id', $id)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function devsOnProject($id)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->innerJoin('u.project', 'up')
            ->where('up.id = :id')
            ->andWhere('u.roles LIKE :val')
            ->setParameter('val', "%ROLE_USER%")
            ->setParameter('id', $id)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
