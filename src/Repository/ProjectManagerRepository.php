<?php

namespace App\Repository;

use App\Entity\ProjectManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProjectManager|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectManager|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectManager[]    findAll()
 * @method ProjectManager[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectManagerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectManager::class);
    }

    // /**
    //  * @return ProjectManager[] Returns an array of ProjectManager objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectManager
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
