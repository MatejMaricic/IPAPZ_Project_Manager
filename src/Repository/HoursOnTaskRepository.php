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

    /**
     * @return HoursOnTask[] Returns an array of HoursOnTask objects
     */

    public function findHoursByProject($id)
    {
        return $this->createQueryBuilder('h')
            ->select('h')
            ->innerJoin('h.project', 'u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(60)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCriteria($project, $date, $billable)
    {
        return $this->createQueryBuilder('h')
            ->select('h')
            ->innerJoin('h.project', 'u')
            ->andWhere('u.id = :project')
            ->andWhere('h.addedAt <= :date')
            ->andWhere('h.billable = :billable')
            ->setParameter('project', $project->getId())
            ->setParameter('date', $date)
            ->setParameter('billable', $billable)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(60)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByDate($date,$project)
    {
        $startDate = $date;
        $endDate =(clone $startDate)->modify('last day of this month')->setTime(23, 59, 59);
        return $this->createQueryBuilder('h')
            ->select('h')
            ->innerJoin('h.project', 'u')
            ->Where('u.id = :project')
            ->andWhere('h.addedAt BETWEEN :start AND :end')
            ->setParameter('project', $project->getId())
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(60)
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @return HoursOnTask[] Returns an array of HoursOnTask objects
     */

    public function findHoursByUser($id)
    {
        return $this->createQueryBuilder('h')
            ->select('h')
            ->innerJoin('h.user', 'u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(60)
            ->getQuery()
            ->getResult()
            ;
    }


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
