<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }


    public function findByUserId($id)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.users', 'u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10);
    }

    public function findByUser($id)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.users', 'u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
