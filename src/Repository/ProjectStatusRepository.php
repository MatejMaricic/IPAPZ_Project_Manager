<?php

namespace App\Repository;

use App\Entity\ProjectStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProjectStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectStatus[]    findAll()
 * @method ProjectStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectStatus::class);
    }

    public function findAllForProject($id)
    {
        return $this->createQueryBuilder('ps')
            ->innerJoin('ps.projects', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(10);
    }
}
